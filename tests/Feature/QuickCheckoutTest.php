<?php

namespace Tests\Feature;

use App\Models\{AccountHead, AccountLedger, Category, Order, OrderDetail, Product, ProductStock, Shipping, User};
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class QuickCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    private $product;
    private $shipping;
    private $phone;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::destroy();
        $this->phone = '019'.random_int(10000000, 99999999);
        $category = Category::create(['name_en' => 'Checkout test', 'slug' => (string) Str::uuid(), 'created_by' => 1]);
        $this->product = Product::create([
            'category_id' => $category->id, 'created_by' => 1, 'slug' => (string) Str::uuid(),
            'name_en' => 'Checkout test product', 'status' => 1, 'stock_qty' => 10,
            'regular_price' => 1000, 'discount_type' => 1, 'discount_price' => 100, 'is_varient' => 0,
        ]);
        $this->shipping = Shipping::create(['name' => 'Test delivery', 'type' => 1, 'shipping_charge' => 80, 'status' => 1]);
        AccountHead::firstOrCreate(['id' => 2], ['title' => 'Orders', 'slug' => 'orders']);
    }

    private function cart($quantity = 2, $options = [])
    {
        Cart::add(['id' => $this->product->id, 'name' => 'Tampered name', 'qty' => $quantity, 'price' => 1, 'weight' => 1, 'options' => $options]);
        return $this->getJson('/quick-checkout')->assertOk()->json();
    }

    private function payload($token)
    {
        return ['name' => 'Test Customer', 'phone' => $this->phone, 'address' => 'Test road, Dhaka', 'shipping_id' => $this->shipping->id, 'token' => $token, 'grand_total' => 1, 'shipping_charge' => 0];
    }

    public function test_guest_order_creates_hashed_account_uses_server_prices_and_is_idempotent()
    {
        $quote = $this->cart();
        $this->assertEquals(1800, $quote['subtotal']);
        $payload = $this->payload($quote['token']);
        $response = $this->postJson('/quick-checkout', $payload)->assertOk()->assertJson(['account_created' => true, 'total' => 1880, 'cartQty' => 0]);
        $order = Order::where('invoice_no', $response->json('invoice'))->firstOrFail();
        $this->get($response->json('invoice_url'))->assertOk()->assertSee($order->invoice_no)->assertSee('1,880.00')->assertSee('Print / Save PDF');
        $this->get(route('checkout.invoice', $order))->assertForbidden();
        $user = User::findOrFail($order->user_id);
        $this->assertTrue(Hash::check('12345678', $user->password));
        $this->assertSame($this->phone, $user->phone);
        $this->assertSame($payload['address'], $user->address);
        $this->assertEquals(8, $this->product->fresh()->stock_qty);
        $this->assertEquals(900, $order->order_details->first()->price);
        $this->assertSame($this->product->name_en, $order->order_details->first()->product_name);
        $this->assertGuest();
        $this->postJson('/quick-checkout', $payload)->assertOk()->assertJson(['invoice' => $order->invoice_no]);
        $this->assertEquals(1, Order::where('invoice_no', $order->invoice_no)->count());
        $this->assertEquals(1, AccountLedger::where('order_id', $order->id)->count());
        $this->assertEquals(8, $this->product->fresh()->stock_qty);
        $this->post('/login', ['email' => '+88'.$this->phone, 'password' => '12345678'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_account_is_reused_without_resetting_password_or_logging_in()
    {
        $user = User::create(['name' => 'Original name', 'phone' => $this->phone, 'role' => 3, 'password' => Hash::make('original-password')]);
        $quote = $this->cart();
        $response = $this->postJson('/quick-checkout', $this->payload($quote['token']))->assertOk()->assertJson(['account_created' => false]);
        $this->assertEquals($user->id, Order::where('invoice_no', $response->json('invoice'))->value('user_id'));
        $this->assertTrue(Hash::check('original-password', $user->fresh()->password));
        $this->assertSame('Original name', $user->fresh()->name);
        $this->assertGuest();
    }

    public function test_stock_changes_and_invalid_area_do_not_create_orders_or_accounts()
    {
        $quote = $this->cart();
        $payload = $this->payload($quote['token']);
        $this->shipping->update(['status' => 0]);
        $this->postJson('/quick-checkout', $payload)->assertStatus(422)->assertJsonValidationErrors('shipping_id');
        $this->shipping->update(['status' => 1]);
        $this->product->update(['stock_qty' => 1]);
        $this->postJson('/quick-checkout', $payload)->assertStatus(422)->assertJsonValidationErrors('cart');
        $this->assertFalse(User::where('phone', $this->phone)->exists());
        $this->assertFalse(Order::where('phone', $this->phone)->exists());
        $this->assertEquals(2, Cart::count());
    }

    public function test_variant_order_uses_variant_price_and_decrements_selected_stock()
    {
        $this->product->update(['is_varient' => 1]);
        $black = ProductStock::create(['product_id' => $this->product->id, 'varient' => 'Black', 'qty' => 3, 'price' => 1200]);
        $white = ProductStock::create(['product_id' => $this->product->id, 'varient' => 'White', 'qty' => 7, 'price' => 1300]);
        $quote = $this->cart(2, ['varient' => 'Black', 'attribute_names' => ['Color'], 'attribute_values' => ['Black']]);
        $response = $this->postJson('/quick-checkout', $this->payload($quote['token']))->assertOk()->assertJson(['total' => 2280]);
        $this->assertEquals(1, $black->fresh()->qty);
        $this->assertEquals(7, $white->fresh()->qty);
        $detail = Order::where('invoice_no', $response->json('invoice'))->first()->order_details->first();
        $this->assertEquals(1100, $detail->price);
        $this->assertStringContainsString('Black', $detail->variation);
    }

    public function test_empty_cart_and_invalid_customer_details_are_rejected()
    {
        $this->getJson('/quick-checkout')->assertStatus(422);
        $quote = $this->cart();
        $payload = $this->payload($quote['token']);
        $payload['phone'] = 'bad-number';
        $this->postJson('/quick-checkout', $payload)->assertStatus(422)->assertJsonValidationErrors('phone');
        $payload = $this->payload((string) Str::uuid());
        $this->postJson('/quick-checkout', $payload)->assertStatus(422)->assertJsonValidationErrors('cart');
    }

    public function test_order_failure_rolls_back_account_order_and_stock()
    {
        $quote = $this->cart();
        $ordersBefore = Order::count();
        OrderDetail::creating(function () { throw new \RuntimeException('Simulated order detail failure'); });
        try {
            app(\App\Services\QuickCheckout::class)->place($this->payload($quote['token']), $quote['token']);
            $this->fail('The simulated failure should abort the transaction.');
        } catch (\RuntimeException $error) {
            $this->assertSame('Simulated order detail failure', $error->getMessage());
        } finally {
            OrderDetail::flushEventListeners();
        }
        $this->assertEquals($ordersBefore, Order::count());
        $this->assertFalse(User::where('phone', $this->phone)->exists());
        $this->assertEquals(10, $this->product->fresh()->stock_qty);
        $this->assertEquals(2, Cart::count());
    }
}

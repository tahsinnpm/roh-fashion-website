<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use App\Models\ProductStock;

class CartController extends Controller
{
    private function sidebarLimits($item, $product)
    {
        $stock = !$product || !$product->status ? 0 : ($product->is_varient
            ? (int) optional($product->stocks->firstWhere('varient', $item->options->varient))->qty
            : (int) $product->stock_qty);
        $others = Cart::content()->filter(function ($other) use ($item, $product) {
            return $other->rowId !== $item->rowId && $other->id == $item->id
                && (!$product || !$product->is_varient || $other->options->varient === $item->options->varient);
        })->sum('qty');

        return [max(1, (int) optional($product)->minimum_buy_qty), max(0, $stock - $others)];
    }

    public function sidebar()
    {
        $products = Product::with('stocks')->whereIn('id', Cart::content()->pluck('id'))->get()->keyBy('id');
        $items = Cart::content()->map(function ($item) use ($products) {
            $product = $products->get($item->id);
            [$minimum, $maximum] = $this->sidebarLimits($item, $product);
            return [
                'rowId' => $item->rowId, 'name' => $item->name, 'quantity' => (int) $item->qty,
                'unitPrice' => (float) $item->price, 'total' => (float) $item->price * $item->qty,
                'image' => asset($item->options->image ?: 'upload/product-default.jpg'),
                'url' => $product ? route('product.details', $product->slug) : null,
                'variant' => $item->options->varient ?: '', 'minimum' => $minimum, 'maximum' => $maximum,
            ];
        })->values();

        return response()->json(['items' => $items, 'cartQty' => Cart::count(), 'subtotal' => (float) Cart::subtotal(2, '.', '')]);
    }

    public function updateSidebar(Request $request, $rowId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $item = Cart::content()->get($rowId);
        abort_unless($item, 404);
        [$minimum, $maximum] = $this->sidebarLimits($item, Product::with('stocks')->find($item->id));
        $quantity = (int) $request->quantity;
        if ($quantity < $minimum || $quantity > $maximum) {
            return response()->json(['message' => $quantity < $minimum ? 'Minimum quantity is '.$minimum.'.' : 'Not enough stock for this quantity.'], 422);
        }
        Cart::update($rowId, $quantity);
        return $this->sidebar();
    }

    public function removeSidebar($rowId)
    {
        abort_unless(Cart::content()->has($rowId), 404);
        Cart::remove($rowId);
        return $this->sidebar();
    }

    public function index(){
        // Header Category Start
        $categories = Category::orderBy('name_en','DESC')->where('status','=',1)->limit(5)->get();
        $carts = Cart::content();
        //dd($carts);
        return view('FrontEnd.cart.index',compact('categories'));
    }
    /* ============ Start AddToCart Methoed ============ */
    public function AddToCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1', 'product_varient' => 'nullable|string']);
        $product = Product::where('status', 1)->findOrFail($id);
        $quantity = (int) $request->quantity;
        if ($quantity < max(1, (int) $product->minimum_buy_qty)) {
            return response()->json(['error' => 'Minimum quantity is '.$product->minimum_buy_qty], 422);
        }
        $options = ['image' => $product->product_thumbnail, 'slug' => $product->slug, 'is_varient' => (int) $product->is_varient];
        $price = $product->storefrontPrice();
        $available = (int) $product->stock_qty;
        if ($product->is_varient) {
            $stock = ProductStock::where('product_id', $id)->where('varient', $request->product_varient)->first();
            if (!$stock) {
                return response()->json(['error' => 'Please select an available combination.'], 422);
            }
            $submitted = json_decode($request->input('options', '[]'), true);
            if (!is_array($submitted)) {
                return response()->json(['error' => 'Please select all options.'], 422);
            }
            $values = collect($submitted)->where('name', 'attribute_options[]')->pluck('value')->all();
            $definitions = json_decode($product->attribute_values ?: '[]') ?: [];
            if (count($values) !== count($definitions) || implode('-', $values) !== $stock->varient) {
                return response()->json(['error' => 'Please select all options.'], 422);
            }
            $ids = []; $names = [];
            foreach ($definitions as $index => $definition) {
                if (!in_array($values[$index], $definition->values, true)) {
                    return response()->json(['error' => 'Invalid product option.'], 422);
                }
                $ids[] = $definition->attribute_id;
                $names[] = optional(\App\Models\Attribute::find($definition->attribute_id))->name;
            }
            $options += ['varient' => $stock->varient, 'attribute_ids' => $ids, 'attribute_names' => $names, 'attribute_values' => $values];
            $price = $product->storefrontPrice($stock->price);
            $available = (int) $stock->qty;
        }
        $existing = Cart::content()->filter(function ($item) use ($id, $product, $request) {
            return $item->id == $id && (!$product->is_varient || $item->options->varient === $request->product_varient);
        })->sum('qty');
        if ($existing + $quantity > $available) {
            return response()->json(['error' => 'Not enough stock.'], 422);
        }
        Cart::add(['id' => $product->id, 'name' => $product->name_en, 'qty' => $quantity, 'price' => $price, 'weight' => 1, 'options' => $options]);
        return response()->json(['success' => 'Added to your cart', 'cartQty' => Cart::count()]);
    }
    /* ============ End AddToCart Methoed =========== */

    /*=================== Start Mini Cart  Methoed ===================*/
    public function AddMiniCart(){

        $carts = Cart::content();
        $cartQty = Cart::count();
        $cartTotal = Cart::total();

        return response()->json(array(
            'carts' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => round($cartTotal),
        ));

    } // end method

    /*=================== End Mini Cart  Methoed ===================*/

    /*=========== Start Remove Mini Cart  Methoed ============*/
    public function RemoveMiniCart($rowId){

        Cart::remove($rowId);
        return response()->json(['success'=> 'Product Removed from Cart']);
    }

    /*============== End Remove Mini Cart  Methoed =============*/

    /* ================= Start GetCartProduct Method =================== */
    public function getCartProduct(){
        $carts = Cart::content();
        $cartQty = Cart::count();
        $cartTotal = Cart::total();

        return response()->json(array(
            'carts' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => $cartTotal,
        ));

    } //end method
    /* ================= End GetCartProduct Method =================== */

    /* ================= Start CartIncrement Method =================== */
    public function cartIncrement($rowId){
        $row = Cart::get($rowId);
        //dd($row);
        $id = $row->id;

        $product = Product::findOrFail($id);

        if(!$product->is_varient){
            $prev_cart_qty = 0;
            $carts = Cart::content();
            foreach($carts as $cart){
                if($cart->id == $id) {
                    $prev_cart_qty += $cart->qty;
                }
            }

            $qty = $prev_cart_qty + $row->qty;

            if($qty > $product->stock_qty){
                return response()->json(['error'=> 'Not enough stock']);
            }
        }else{
            $prev_cart_qty = 0;
            $carts = Cart::content();
            foreach($carts as $cart){
                if($cart->id == $id) {
                    if($cart->options->varient == $row->product_varient){
                        $prev_cart_qty += $cart->qty;
                    }
                }
            }

            $qty = $prev_cart_qty + $row->qty;
            $stock = ProductStock::where('product_id', $id)->where('varient', $row->options->varient)->first();

            if($qty > $stock->qty){
                return response()->json(['error'=> 'Not enough stock']);
            }
        }

        Cart::update($rowId, $row->qty + 1);

        return response()->json(['success'=> 'Successfully Added on Your Cart']);

    } // end mehtod

    /* ================= End CartIncrement Method =================== */

    /* ================= Start CartDecrement Method =================== */
    public function cartDecrement($rowId){
        $row = Cart::get($rowId);
        if ($row->qty > 0) {
            Cart::update($rowId, $row->qty - 1);
            return response()->json(['success' => 'Successfully Decremented']);
        } else {
            return response()->json(['error' => 'Cannot decrement below zero']);
        }
    }

    /* ================= End CartDecrement Method =================== */

    /* ================= Start RemoveCartProduct Method ============== */
    public function removeCartProduct($rowId){

        Cart::remove($rowId);
        return response()->json(['success' => 'Successfully Remove From Cart']);
    } // end method

    /* =============== Start RemoveCartProduct Method ============= */

    /* ================= Start Destroy Method ============== */
    public function destroy()
    {
        Cart::destroy();
        Session::flash('success','Cart Permanently Deleted Successfully.');
        return back();
    } // end method

    /* ================= Start Destroy Method ============== */
}

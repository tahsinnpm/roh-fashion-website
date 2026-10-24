<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\Address;
use App\Models\District;
use App\Models\Upazilla;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use App\Utility\SendSMSUtility;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\AccountHead;
use App\Models\AccountLedger;
use App\Http\Controllers\Frontend\PublicSslCommerzPaymentController;
use App\Models\Shipping;
use App\Models\Coupon;


class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->to(route('home').'#checkout');
    }

    public function shippingAjax($shipping_id){
        $shipping = Shipping::find($shipping_id);
        return json_encode($shipping);
    }

    /* ============== Start GetCheckoutProduct Method ============= */
    public function getCheckoutProduct(){
        $carts = Cart::content();
        $cartQty = Cart::count();
        $cartTotal = Cart::total();

        return response()->json(array(
            'carts' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => $cartTotal,
        ));

    } //end method
    /* ============= End GetCheckoutProduct Method ============== */

    /* ============= Start getdivision Method ============== */
    public function getdivision(){
    $division = District::where('division_id', $_GET['division_id'])->orderBy('district_name_en','ASC')->get();

        return json_encode($division);
    }
    /* ============= End getdivision Method ============== */

    /* ============= Start getupazilla Method ============== */
    public function getupazilla(){
        $upazilla = Upazilla::where('district_id', $_GET['district_id'])->orderBy('name_en','ASC')->get();

        return json_encode($upazilla);
    }
    /* ============= End getupazilla Method ============== */



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //  dd($request->all());
        $data = $request->validate([
            'name' => 'required|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => ['required','regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/','min:11','max:15'],
            'address' => 'required|max:10000',
            'payment_option'=>'required',
            'comment' => 'nullable|max:2000',
        ]);
        // dd(Cart::total()+$request->total_amount);
        // dd($request->total_amount);
        // if($request->payment_option == 'nagad'){
        //     return redirect()->route('checkout.payment');
        // }
        $carts = Cart::content();
        //dd($carts);

        if($carts->isEmpty()){
            $notification = array(
                'message' => 'Your cart is empty.',
                'alert-type' => 'error'
            );
            return redirect()->route('home')->with($notification);
        }

        // dd($request->all());

        if(Auth::check()){
            $user_id = Auth::id();
            $type = 1;
        }else{
            $user_id = 1;
            $type = 2;
        }

        if($request->payment_option == 'cod' || $request->payment_option == 'wallet'){
            $payment_status = 0;
            
        }else{
            $payment_status = 1;
        }

        $invoice_data = Order::orderBy('id','desc')->first();

        if($invoice_data){
            $lastId = $invoice_data->id;
            // $idd = str_replace("E-", "", $lastId);
            $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
            $invoice_no = $id;
        }else{
            $invoice_no = "0000001";
        }

      if (session()->has('affiliate_ref')) {
            $ref = session('affiliate_ref');
            $affiliate = \App\Models\Affiliate::where('referral_code', $ref)->first();
            if($affiliate){
                $affiliateId = $affiliate->id;
            }
        } else {
            $affiliateId = null;
        }

        // order add //
        $order = Order::create([
            'user_id' => $user_id,
            'affiliate_id' => $affiliateId,
            'name' => $request->name,
            'sub_total' => $request->sub_total,
            'grand_total' => $request->grand_total,
            'shipping_charge' => $request->shipping_charge,
            'shipping_name' => $request->shipping_name,
            'shipping_type' => $request->shipping_type,
            'payment_method' => $request->payment_option,
            'payment_status' => $payment_status,
            // 'invoice_no' => date('Ymd-His') . rand(10, 99),
            'invoice_no' => $invoice_no,
            'delivery_status' => 'pending',
            'phone' => $request->phone,
            'email' => $request->email,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'upazilla_id' => $request->upazilla_id,
            'address' => $request->address,
            'comment' => $request->comment,
            'coupon' => Session::get('amount'),
            'type' => $type,
            //'created_by' => Auth::guard('admin')->user()->id,
        ]);
        $coupon = Coupon::where('id', Session::get('couponId'))
            ->where('status', 1)
            ->where('total_use_limit', '>', 0)
            ->first();

        if ($coupon) {
            // Decrement total_use_limit
            $coupon->decrement('total_use_limit');
        }

        // if(get_setting('otp_system')){
        //     $sms_template = SmsTemplate::where('name','order_message')->where('status',1)->first();
        //     if($sms_template){
        //         $sms_body       = $sms_template->body;
        //         $sms_body       = str_replace('[[order_code]]', $order->invoice_no, $sms_body);
        //         $sms_body       = str_replace('[[order_amount]]', $order->grand_total, $sms_body);
        //         $sms_body       = str_replace('[[site_name]]', env('APP_NAME'), $sms_body);

        //         if($order->pay_status == 1){
        //             $payment_info = json_decode($order->payment_info);
        //             $sms_body     = str_replace('[[payment_details]]', 'পেমেন্ট স্ট্যাটাসঃ paid'.', ট্রান্সেকশন আইডিঃ '.$order->transaction_id.', মাধ্যমঃ '.$order->payment_method.' ', $sms_body);
        //         }else{
        //             $sms_body       = str_replace('[[payment_details]]', '', $sms_body);
        //         }

        //         if(substr($order->phone,0,3)=="+88"){
        //             $phone = $order->phone;
        //         }elseif(substr($order->phone,0,2)=="88"){
        //             $phone = '+'.$order->phone;
        //         }else{
        //             $phone = '+88'.$order->phone;
        //         }
        //         //dd($phone);
        //         SendSMSUtility::sendSMS($phone, $sms_body);

        //         // $sms_body = str_replace('আপনার', 'নতুন', $sms_body);
        //         // $setting = Setting::where('name', 'phone')->first();
        //         // if($setting->value != null){
        //         //     $admin_phone=$setting->value;

        //         //     if(substr($admin_phone,0,3)=="+88"){
        //         //         $phone = $admin_phone;
        //         //     }elseif(substr($admin_phone,0,2)=="88"){
        //         //         $phone = '+'.$admin_phone;
        //         //     }else{
        //         //         $phone = '+88'.$admin_phone;
        //         //     }
        //         //     SendSMSUtility::sendSMS($admin_phone, $sms_body);
        //         // }
        //     }
        // }


        // order details add //
        foreach ($carts as $cart) {
            // dd($cart);
            $vendor_id = 0;
            $product = Product::find($cart->id);
            if($product->vendor_id !=0){
                $vendor_id = $product->vendor_id;
            }
            $opt = is_array($cart->options) ? $cart->options : (method_exists($cart->options, 'toArray') ? $cart->options->toArray() : (array)$cart->options);
            if(!empty($opt['is_varient']) && $opt['is_varient'] == 1){
                $variations = array();
                $attrNames = $opt['attribute_names'] ?? [];
                $attrValues = $opt['attribute_values'] ?? [];
                if(!empty($attrNames) && is_array($attrNames)){
                    foreach($attrNames as $i => $name){
                        $item = [
                            'attribute_name' => $name,
                            'attribute_value' => $attrValues[$i] ?? ($opt['varient'] ?? ''),
                        ];
                        array_push($variations, $item);
                    }
                }
                if(empty($variations) && !empty($opt['varient'])){
                    $variations[] = [
                        'attribute_name' => 'Variant',
                        'attribute_value' => $opt['varient'],
                    ];
                }
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'product_id' => $cart->id,
                    'vendor_id' => $vendor_id,
                    'product_name' => $cart->name,
                    'is_varient' => 1,
                    'variation' => json_encode($variations, JSON_UNESCAPED_UNICODE),
                    'qty' => $cart->qty,
                    'price' => $cart->price,
                    'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);

                // stock calculation //
                $variantStr = $opt['varient'] ?? '';
                $stock = ProductStock::where('product_id', $product->id)->where('varient', $variantStr)->first();
                if(!$stock && !empty($variantStr)){
                    $cleanV = preg_replace('/\s+/', ' ', trim($variantStr));
                    foreach(ProductStock::where('product_id', $product->id)->get() as $s){
                        if(preg_replace('/\s+/', ' ', trim($s->varient)) === $cleanV){
                            $stock = $s;
                            break;
                        }
                    }
                }
                if($stock){
                    $stock->qty = max(0, $stock->qty - $cart->qty);
                    $stock->save();
                }

            }else{
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'product_id' => $cart->id,
                    'vendor_id' => $vendor_id,
                    'product_name' => $cart->name,
                    'is_varient' => 0,
                    'qty' => $cart->qty,
                    'price' => $cart->price,
                    'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);
            }

            $product->stock_qty = $product->stock_qty - $cart->qty;
            $product->save();
        }

        Cart::destroy();
        Session::forget('couponCode');
        Session::forget('discountType');
        Session::forget('amount');
        Session::forget('affiliate_ref');
        //Ledger Entry
        $prev_balance =AccountLedger::orderBy('id', 'desc')->first();
        if(!$prev_balance){
            $balance = 0;
        }
        else{
            $balance = $prev_balance->balance;
        }

        $ledger_balance = $balance + $order->grand_total;
        $ledger = AccountLedger::create([
            'account_head_id' => 2,
            'particulars' => 'Order ID: '.$order->id,
            'credit' => $order->grand_total,
            'order_id' => $order->id,
            'balance' => $ledger_balance,
            'type' => 2,
        ]);
//        $ledger->save();

        $notification = array(
            'message' => 'Your order has been placed successfully.',
            'alert-type' => 'success'
        );

        return redirect()->route('checkout.success', $order->invoice_no)->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::where('invoice_no', $id)->first();
        if ($order) {
            return redirect()->to(\Illuminate\Support\Facades\URL::signedRoute('checkout.invoice', ['order' => $order->id]));
        }

        $notification = array(
            'message' => 'Your Order Successfully.',
            'alert-type' => 'success'
        );

        return view('FrontEnd.order.order_view', compact('order'))->with($notification);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // public function Myorders(){

    //     $orders = Order::where('user_id',Auth::id())->orderBy('id','DESC')->get();
    //     return view('frontend.user.order.order_view',compact('orders'));

    // }

    public function payment(Request $request){
        //dd($request->payment_option);
        // dd($request);
        if($request->payment_option == 'cod' || $request->payment_option == 'wallet'){
            $checkout = new CheckoutController;
            return $checkout->store($request);
        }

        $payment_method = $request->payment_option;
        $total_amount = Cart::total();
        $last_order = Order::orderBy('id','DESC')->first();
        $order_id = $last_order->id+1;
        $invoice_no = date('YmdHi').$order_id;
        Session::put('invoice_no', $invoice_no);

        // dd('oks');

        if($request->payment_option == 'cod' || $request->payment_option == 'wallet'){
            return redirect()->route('checkout.store');
        }else{
            // dd('ss');
            Session::put('payment_method', $request->payment_option);
            Session::put('payment_type', 'cart_payment');
            Session::put('payment_amount', $total_amount);
            // dd('ok');
            if($request->payment_option == 'nagad'){
                $nagad = new NagadController;
                return $nagad->getSession();
            }else if($request->payment_option == 'bkash'){
                // dd('okb');
                $bkash = new BkashController;
                return $bkash->pay();
            }elseif ($request->payment_option == 'sslcommerz') {
                $sslcommerz = new PublicSslCommerzPaymentController;
                return $sslcommerz->index($request);
            }elseif($payment_method =='aamarpay'){
                $aamarpay = new AamarpayController;
                return $aamarpay->index();
            }
        }

        Session::forget('couponCode');
        Session::forget('discountType');
        Session::forget('amount');
        Session::forget('status');

        return view('FrontEnd.checkout.payment', compact('payment_method', 'total_amount', 'invoice_no'));
    }

    public function success($orderId, $json){

        dd(json_decode($json));
    }
}

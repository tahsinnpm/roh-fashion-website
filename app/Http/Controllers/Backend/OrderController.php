<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AccountLedger;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorComission;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Address;
use App\Models\Affiliate;
use App\Models\District;
use App\Models\Upazilla;
use App\Models\Shipping;
use App\Services\OrderInventory;
use Illuminate\Support\Facades\DB;
use Session;
use PDF;
use function Symfony\Component\VarDumper\Dumper\esc;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->date;
        $delivery_status = null;
        $payment_status = null;

        //dd($request);

        //$orders = Order::orderBy('id', 'desc');

        // if ($request->delivery_status != null) {
        //     $orders = $orders->where('delivery_status', $request->delivery_status);
        //     // dd($orders);
        //     $delivery_status = $request->delivery_status;
        // }

        // if ($request->payment_status != null) {
        //     $orders = $orders->where('payment_status', $request->payment_status);
        //     $payment_status = $request->payment_status;
        // }

        // if ($date != null) {
        //     $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])));
        // }

        if($request->delivery_status != null && $request->payment_status != null && $date != null){

            $orders = Order::where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])))->where('delivery_status', $request->delivery_status)->where('payment_status', $request->payment_status);

            $delivery_status = $request->delivery_status;
            $payment_status = $request->payment_status;

        }else if($request->delivery_status == null && $request->payment_status == null && $date == null){
            $orders = Order::orderBy('id', 'desc');
        }else{
            if($request->delivery_status == null){
                if($request->payment_status != null && $date != null){
                    $orders = Order::where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])))->where('payment_status', $request->payment_status);
                    $payment_status = $request->payment_status;
                }else if($request->payment_status == null && $date != null){
                    $orders = Order::where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])));
                }else{
                    $orders = Order::where('payment_status', $request->payment_status);
                    $payment_status = $request->payment_status;
                }
            }else if($request->payment_status == null){
                if($request->delivery_status != null && $date != null){
                    $orders = Order::where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])))->where('delivery_status', $request->delivery_status);
                    $delivery_status = $request->delivery_status;
                }else if($request->delivery_status == null && $date != null){
                    $orders = Order::where('created_at', '>=', date('Y-m-d', strtotime(explode(" - ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" - ", $date)[1])));
                }else{
                    $orders = Order::where('delivery_status', $request->delivery_status);
                    $delivery_status = $request->delivery_status;
                }
            }else if($request->date == null){
                if($request->delivery_status != null && $request->payment_status != null){
                    $orders = Order::where('delivery_status', $request->delivery_status)->where('payment_status', $request->payment_status);
                    $delivery_status = $request->delivery_status;
                    $payment_status = $request->payment_status;
                }else if($request->delivery_status == null && $request->payment_status != null){
                    $orders = Order::where('payment_status', $request->payment_status);
                    $payment_status = $request->payment_status;
                }else{
                    $orders = Order::where('delivery_status', $request->delivery_status);
                    $delivery_status = $request->delivery_status;
                }
            }
        }

        //dd($request);

        $orders = $orders->with('order_details.product')->paginate(15);
        return view('backend.sales.all_orders.index', compact('orders', 'delivery_status', 'date','payment_status'));
    }

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        return AccountLedger::latest()->first();
        $order = Order::findOrFail($id);
        $shippings = Shipping::where('status', 1)->get();

        return view('backend.sales.all_orders.show', compact('order', 'shippings'));
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
//        return $request;
        $this->validate($request,[
            'payment_method' => 'required',
        ]);
        $order = Order::findOrFail($id);

        if ($request->has('address')) {
            $order->address = $request->address;
        }
        $order->division_id = $request->division_id ?: null;
        $order->district_id = $request->district_id ?: null;
        $order->upazilla_id = $request->upazilla_id ?: null;
        $order->payment_method = $request->payment_method;
        if ($request->filled('email')) {
            $order->email = $request->email;
        }

        $discount_total = ($order->sub_total - $request->discount);
        $total_ammount = ($discount_total + $request->shipping_charge);

        $order->shipping_charge = $request->shipping_charge;
        $order->discount        = $request->discount;
        $order->grand_total     = $total_ammount;
        $order->save();

        Session::flash('success','Admin Orders Information Updated Successfully');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderInventory $inventory, $id)
    {
        try {
            DB::transaction(function () use ($id, $inventory) {
                $order = Order::lockForUpdate()->findOrFail($id);
                if (!$order->stock_restored_at) {
                    $inventory->restore($order);
                    $order->stock_restored_at = now();
                    $order->save();
                }
                $order->delete();
            });
        } catch (\RuntimeException $error) {
            return back()->with('error', $error->getMessage());
        }

        $notification = array(
            'message' => 'Order moved to trash. Stock quantities have been returned.',
            'alert-type' => 'success'
        );
        return redirect()->route('all_orders.index')->with($notification);
    }

    public function trash()
    {
        $orders = Order::onlyTrashed()->with('order_details')->latest('deleted_at')->paginate(20);
        return view('backend.sales.all_orders.trash', compact('orders'));
    }

    public function restore(OrderInventory $inventory, $id)
    {
        try {
            DB::transaction(function () use ($id, $inventory) {
                $order = Order::withTrashed()->lockForUpdate()->findOrFail($id);
                if (!$order->trashed()) return;

                if ($order->stock_restored_at && $order->delivery_status !== 'cancelled') {
                    $inventory->deduct($order);
                    $order->stock_restored_at = null;
                }

                $order->restore();
                $order->save();
            });
        } catch (\RuntimeException $error) {
            return back()->with('error', $error->getMessage());
        }

        return redirect()->route('orders.trash')->with('success', 'Order restored.');
    }

    public function forceDelete($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::onlyTrashed()->lockForUpdate()->findOrFail($id);
            OrderDetail::where('order_id', $order->id)->delete();
            \App\Models\ReturnRequest::where('order_id', $order->id)->delete();
            $order->forceDelete();
        });

        return redirect()->route('orders.trash')->with('success', 'Order permanently deleted.');
    }


    /*================= Start update_payment_status Methoed ================*/
    public function update_payment_status(Request $request)
    {
      
        $commission_rate=get_setting('commission_rate')->value;
      //  dd($commission_rate);
        $order = Order::findOrFail($request->order_id);
        $affiliateId=$order->affiliate_id;
        $order->payment_status = $request->status;
   
        if($order->payment_status == 1){
            $affiliate = Affiliate::find($affiliateId);
            if ($affiliate) {
                $affiliate->total_earning += $order->grand_total * ($commission_rate / 100);
                $affiliate->save();
            }
            
            if($order->payment_method == 'wallet'){
                $user = User::find($order->user_id);
                $user->points -= $order->grand_total;
                $user->save();
            
            }

        }
       
        $order->save();
        if($order->user_id != 1){
            $this->addOrRemovePoints($request->order_id);
        }

        return response()->json(['success'=> 'Payment status has been updated']);

        // dd($order);
    }
    public function addOrRemovePoints($id)
    {
        $order = Order::findOrFail($id);
        $user = User::findOrfail($order->user_id);
        if ($user->role == 3){
            $orderedProducts = OrderDetail::where('order_id', $order->id)->get();
            $point = 0;
            foreach ($orderedProducts as $item){
                $point += $item->product->points * $item->qty;
            }
            if($order->payment_status == 1){
                $user->points += $point;
            }
            else{
                $user->points -= $point;
            }
            $user->save();
        }
    }

    /*================= Start update_delivery_status Methoed ================*/
    public function update_delivery_status(Request $request, OrderInventory $inventory)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|in:pending,confirmed,shipped,picked_up,on_the_way,delivered,cancelled',
        ]);

        try {
            [$order, $wasCancelled] = DB::transaction(function () use ($request, $inventory) {
                $order = Order::lockForUpdate()->findOrFail($request->order_id);
                $wasCancelled = $order->delivery_status === 'cancelled';

                if ($request->status === 'cancelled' && !$order->stock_restored_at) {
                    $inventory->restore($order);
                    $order->stock_restored_at = now();
                } elseif ($request->status !== 'cancelled' && $order->stock_restored_at) {
                    $inventory->deduct($order);
                    $order->stock_restored_at = null;
                }

                $order->delivery_status = $request->status;
                $order->save();
                return [$order, $wasCancelled];
            });
        } catch (\RuntimeException $error) {
            return response()->json(['error' => $error->getMessage()], 422);
        }

        if($order->user_id != 1){
            $this->addOrRemoveCommission($request->order_id);
        }
        if ($order->delivery_status == 'cancelled' && !$wasCancelled){
            $this->deductAdminBalance($order->id, $order->grand_total);
        }

        return response()->json(['success'=> 'Delivery status has been updated']);

    }

    public function addOrRemoveCommission($id)
    {

        $orderedProducts = OrderDetail::where('order_id', $id)->get();
        $order = Order::findOrFail($id);

        foreach ($orderedProducts as $item){
            if($item->product->vendor_id != 0)
            {
                $vendor = Vendor::findOrFail($item->product->vendor_id);
                if($vendor->commission != 0)
                {
                    if($vendor->comission_type == 2)
                    $adminCommission = ($item->qty * $item->price)*($vendor->commission/100);
                    else{
                        $adminCommission = ($item->qty * $item->price) - $vendor->commission;
                    }
                    $vendorCommission= ($item->qty * $item->price) - $adminCommission;
                    $transaction = VendorTransaction::where('invoice_no', $order->invoice_no)->where('add_amount', '!=', null)->first();
                    if(!$transaction)
                    {
                        if ($order->delivery_status == 'delivered') {
                            $vendor->balance += $vendorCommission;

                            $commission = new VendorComission();
                            $commission->order_id = $id;
                            $commission->vendor_id = $vendor->id;
                            $commission->vendor_amount = $vendorCommission;
                            $commission->admin_commission = $adminCommission;
                            $commission->month = date('m');
                            $commission->year = date('Y');
                            $commission->save();

                            $transaction = new VendorTransaction();
                            $transaction->vendor_id = $vendor->id;
                            $transaction->add_amount = $vendorCommission;
                            $transaction->invoice_no = $order->invoice_no;
                            $transaction->month = date('m');
                            $transaction->year = date('Y');
                            $transaction->status = 1;

                            $transaction->save();
                        }


                    }
                    elseif($transaction != null)
                    {
                        if($order->delivery_status == 'cancelled'){
                            $vendor->balance -= $vendorCommission;
                            VendorComission::where('order_id', $id)->delete();

                            $transaction = new VendorTransaction();
                            $transaction->vendor_id = $vendor->id;
                            $transaction->deduct_amount = $vendorCommission;
                            $transaction->invoice_no = $order->invoice_no;
                            $transaction->month = date('m');
                            $transaction->year = date('Y');
                            $transaction->status = 3;
                            $transaction->save();
                        }
                    }

                    $vendor->save();

                }

            }
        }
    }

    public function deductAdminBalance($id, $grandTotal)
    {

        $prev_balance =AccountLedger::orderBy('id', 'desc')->first();


        $account_ledger = new AccountLedger();
        $account_ledger->account_head_id = 22;
        $account_ledger->particulars = 'Order-' . $id. 'Cancellation';
        $account_ledger->type = 1;
        $account_ledger->order_id = $id;
        $account_ledger->debit = $grandTotal;
        $account_ledger->balance = $prev_balance->balance - $grandTotal;

        $account_ledger->save();
    }

    /*================= Start admin_user_update Methoed ================*/
    public function admin_user_update(Request $request, $id)
    {
        $user = Order::where('id',$id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // dd($user);

        Session::flash('success','Customer Information Updated Successfully');
        return redirect()->back();
    }

    /* ============= Start getdivision Method ============== */
    public function getdivision($division_id){
        $division = District::where('division_id', $division_id)->orderBy('district_name_en','ASC')->get();

        return json_encode($division);
    }
    /* ============= End getdivision Method ============== */

    /* ============= Start getupazilla Method ============== */
    public function getupazilla($district_id){
        $upazilla = Upazilla::where('district_id', $district_id)->orderBy('name_en','ASC')->get();

        return json_encode($upazilla);
    }
    /* ============= End getupazilla Method ============== */

    /* ============= Start invoice_download Method ============== */
    // public function invoice_download($id){
    //     $order = Order::findOrFail($id);

    //     $pdf = PDF::loadView('backend.invoices.invoice',compact('order'))->setPaper('a4')->setOptions([
    //             'tempDir' => public_path(),
    //             'chroot' => public_path(),
    //     ]);
    //     return $pdf->download('invoice.pdf');
    // } // end method

    /* ============= Start invoice_download Method ============== */
    public function invoice_download($id){
        $order = Order::findOrFail($id);
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $tempDirectory = storage_path('app/mpdf');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 14,
            'tempDir' => $tempDirectory,
            'fontdata' => $defaultFontConfig['fontdata'],
            'default_font' => 'freeserif',
        ]);
        $pdf->SetTitle('Invoice ' . $order->invoice_no);
        $pdf->WriteHTML(view('backend.invoices.invoice', compact('order'))->render());

        return response($pdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-' . $order->invoice_no . '.pdf"',
        ]);
    } // end method
    /* ============= End invoice_download Method ============== */

}

<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Str;
class AffiliateController extends Controller
{

     public function pageAffiliate()
    {
         return view('FrontEnd.affiliate.index');
    }

    public function registerAffiliate()
    {
        return view('FrontEnd.affiliate.register');
    }

    public function registerAffiliateStore(Request $request)
    {
     
        $request->validate([
            
            'name' => 'required',
            'phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'min:11', 'max:15'],
            'email' => 'required|string|email|max:255|unique:affiliates',
            'affiliate_code' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

               if ($request->hasfile('profile_image')) {
    $image = $request->file('profile_image');
    $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
    $directory = public_path('upload/affiliate/');
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
    $image->move($directory, $name_gen);  // Correct method
    $profile_image = 'upload/affiliate/' . $name_gen; // Store relative path for public access
} else {
    $profile_image = '';
}

        $refferalCode = 'OHBD' . strtoupper(Str::random(rand(5, 6)));
        $affiliate = Affiliate::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'affiliate_member_id' => $request->affiliate_code,
            'password' => Hash::make($request->password),
            'profile_image' => $profile_image,
            'referral_code' => $refferalCode,
            'status' => 1
        ]);
    
        if ($affiliate) {
            $notification = array(
                'message' => 'Affiliate Registered Successfully.',
                'alert-type' => 'success'
            );
            return redirect()->route('login.affiliate')->with($notification);
        }

    }

    public function loginAffiliate()
    {
        return view('FrontEnd.affiliate.login');
    }

    public function loginAffiliateSubmit(Request $request)
    {

      // dd($request->all());
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('affiliate')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('affiliate.dashboard');
        } else {
            $notification = array(
                'message' => 'Invalid Email or Password.',
                'alert-type' => 'error'
            );
            return redirect()->route('login.affiliate')->with($notification);
        }
    }

    public function dashboard()
{
    $affiliate = auth('affiliate')->user();
    $products=Product::where('status',1)->where('is_affiliate',1)->latest()->get();

    // Example dummy data, replace with real queries
    $totalReferrals = Order::where('affiliate_id', $affiliate->id)->where('payment_status', 1)->count();
    $totalCommissions = $affiliate->total_earning;
    $pendingReferrals = Order::where('affiliate_id', $affiliate->id)->where('payment_status', 0)->count();

    return view('FrontEnd.affiliate.dashboard', compact('totalReferrals', 'totalCommissions', 'pendingReferrals','products','affiliate'));
}

}

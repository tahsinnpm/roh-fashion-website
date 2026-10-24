<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipping;
use App\Models\District;
use App\Models\AccountLedger;
use App\Models\Affiliate;
use App\Models\Attribute;
use App\Models\LandingPage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\AbandonedCheckoutCapture;

class LandingController extends Controller
{
    /**
     * Display Turbo Fan landing page with dynamic product & shipping info.
     */
    public function turboFan()
    {
        // Fetch Product 206 with stocks, category, and brand
        $product = Product::with(['stocks', 'category', 'brand'])->find(206);

        // Fetch dynamic shipping rates from database
        $shipping_inside = Shipping::where('type', 1)->where('status', 1)->first();
        $shipping_outside = Shipping::where('type', 2)->where('status', 1)->first();
        $shippings = Shipping::where('status', 1)->get();

        return view('FrontEnd.landing.turbo-fan', compact('product', 'shipping_inside', 'shipping_outside', 'shippings'));
    }

    /**
     * Display dynamic custom landing page by slug.
     */
    public function showDynamicLanding($slug)
    {
        $landingPage = LandingPage::with(['product.stocks', 'product.category', 'product.brand', 'product.multi_imgs'])
            ->where('slug', $slug)
            ->firstOrFail();

        $product = $landingPage->product;
        if (!$product) {
            abort(404, 'Product not found');
        }

        // Parse attributes and values
        $structuredAttributes = [];
        if ($product->is_varient == 1 && !empty($product->attributes)) {
            $attrIds = is_array($product->attributes) ? $product->attributes : json_decode($product->attributes, true);
            $attrValues = is_array($product->attribute_values) ? $product->attribute_values : json_decode($product->attribute_values, true);

            if (!empty($attrIds) && is_array($attrIds)) {
                foreach ($attrIds as $attrId) {
                    $attrModel = Attribute::find($attrId);
                    $values = [];
                    if (!empty($attrValues) && is_array($attrValues)) {
                        foreach ($attrValues as $valObj) {
                            if (isset($valObj['attribute_id']) && $valObj['attribute_id'] == $attrId) {
                                $values = $valObj['values'] ?? [];
                                break;
                            }
                        }
                    }

                    $attrNameEn = $attrModel ? $attrModel->name : 'Attribute';
                    $attrNameBn = $attrNameEn;
                    if (strtolower($attrNameEn) == 'size') $attrNameBn = 'সাইজ';
                    elseif (strtolower($attrNameEn) == 'fabric') $attrNameBn = 'ফ্যাব্রিক';
                    elseif (strtolower($attrNameEn) == 'color') $attrNameBn = 'কালার';

                    $structuredAttributes[] = [
                        'id'      => $attrId,
                        'name'    => $attrNameEn,
                        'name_bn' => $attrNameBn,
                        'values'  => $values,
                    ];
                }

                // Place Size at the very end of attributes
                usort($structuredAttributes, function ($a, $b) {
                    $aIsSize = strtolower($a['name']) === 'size';
                    $bIsSize = strtolower($b['name']) === 'size';
                    if ($aIsSize && !$bIsSize) return 1;
                    if (!$aIsSize && $bIsSize) return -1;
                    return 0;
                });
            }
        }

        $productStocks = $product->stocks;

        // Fetch dynamic shipping rates from database (website shipping charge)
        $shipping_inside = Shipping::where('type', 1)->where('status', 1)->first();
        $shipping_outside = Shipping::where('type', 2)->where('status', 1)->first();
        $shippings = Shipping::where('status', 1)->get();

        $delivery_inside = $shipping_inside ? (float)$shipping_inside->shipping_charge : (float)($landingPage->delivery_charge_dhaka ?? 80.00);
        $delivery_outside = $shipping_outside ? (float)$shipping_outside->shipping_charge : (float)($landingPage->delivery_charge_outside ?? 150.00);

        // Slide images strictly from product primary image & multi-images first
        $sliderImages = [];
        if ($product->product_thumbnail) {
            $sliderImages[] = $product->product_thumbnail;
        }
        if ($product->multi_imgs && $product->multi_imgs->count() > 0) {
            foreach ($product->multi_imgs as $m) {
                if (!empty($m->photo_name) && !in_array($m->photo_name, $sliderImages)) {
                    $sliderImages[] = $m->photo_name;
                }
            }
        }
        if (empty($sliderImages) && !empty($landingPage->slider_images)) {
            $sliderImages = is_array($landingPage->slider_images) ? $landingPage->slider_images : (json_decode($landingPage->slider_images, true) ?: []);
        }

        $overviewImage = $landingPage->overview_image ?: ($sliderImages[0] ?? $product->product_thumbnail);
        $features = $landingPage->features ?: ['ক্যাশ অন ডেলিভারি', 'সারাদেশে ডেলিভারি', 'এক্সচেঞ্জ সুবিধা'];
        $overviewPoints = $landingPage->overview_points ?: [];
        $reviews = $landingPage->reviews ?: [];
        $faqs = $landingPage->faqs ?: [];

        $aboutCards = is_array($landingPage->about_cards) ? $landingPage->about_cards : [];
        $whatYouGetTitle = $aboutCards['what_you_get_title'] ?? 'যা যা পাচ্ছেন';
        $whatYouGetCards = $aboutCards['what_you_get_cards'] ?? [
            ['icon' => '✦', 'title' => 'সিলভার সিরোস্কি স্টোন', 'subtitle' => 'পুরো শাড়ি জুড়ে ঝলমলে কাজ'],
            ['icon' => '吕', 'title' => 'সিল্ক ব্লেন্ড শিফন', 'subtitle' => 'আল্ট্রা সফট ও লাইটওয়েট'],
            ['icon' => '💎', 'title' => 'নেকলেস ডিজাইন ব্লাউজ', 'subtitle' => 'ম্যাচিং জর্জেট ব্লাউজ পিস'],
        ];

        return view('FrontEnd.landing.dynamic', compact(
            'landingPage',
            'product',
            'structuredAttributes',
            'productStocks',
            'delivery_inside',
            'delivery_outside',
            'shipping_inside',
            'shipping_outside',
            'shippings',
            'sliderImages',
            'overviewImage',
            'features',
            'overviewPoints',
            'reviews',
            'faqs',
            'whatYouGetTitle',
            'whatYouGetCards'
        ));
    }

    /**
     * Universal Order Store method for all landing pages.
     * Can handle single products, variant selections, dynamic shipping calculation,
     * inventory deduction, and ledger updates.
     */
    public function orderStore(Request $request, AbandonedCheckoutCapture $abandonedCheckoutCapture)
    {
        // 1. Validation
        $rules = [
            'product_id' => 'required|exists:products,id',
            'name'       => 'required|string|max:191',
            'phone'      => ['required', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'address'    => ['required', 'string', 'max:1000', 'regex:/\\S+(?:\\s+\\S+){1,}/u'],
            'district'   => 'nullable|string|max:191',
            'area'       => 'nullable|string|max:191',
            'quantity'   => 'nullable|integer|min:1|max:100',
            'qty'        => 'nullable|integer|min:1|max:100',
            'color'      => 'nullable|string|max:100',
            'varient'    => 'nullable|string|max:100',
            'comment'    => 'nullable|string|max:2000',
        ];

        // Resolve landing page email requirements
        $landingPage = null;
        if ($request->filled('landing_id')) {
            $landingPage = LandingPage::find($request->landing_id);
        } elseif ($request->filled('landing_slug')) {
            $landingPage = LandingPage::where('slug', $request->landing_slug)->first();
        }

        if ($landingPage && $landingPage->getEmailFieldMode() === 'required') {
            $rules['email'] = 'required|email|max:191';
        } else {
            $rules['email'] = 'nullable|email|max:191';
        }

        $request->validate($rules, [
            'address.required' => 'Please enter your full address, including your house or road, area, thana, and district.',
            'address.regex' => 'Please enter at least two parts of your address, separated by a space.',
            'email.required' => 'আপনার ইমেইল অ্যাড্রেসটি প্রদান করুন।',
            'email.email'    => 'সঠিক ফরম্যাটের ইমেইল অ্যাড্রেস লিখুন (যেমন: name@gmail.com)।',
        ]);

        $quantity = $request->filled('quantity') ? (int)$request->quantity : ((int)$request->qty ?: 1);
        $productId = $request->product_id;
        $selectedVariant = $request->varient ?: $request->color;
        if (!$selectedVariant) {
            $attrValues = [];
            foreach ($request->all() as $k => $v) {
                if (str_starts_with($k, 'attr_') && !empty($v)) {
                    $attrValues[] = trim($v);
                }
            }
            if (!empty($attrValues)) {
                $selectedVariant = implode('-', $attrValues);
            }
        }

        try {
            DB::beginTransaction();

            // 2. Fetch Product
            $product = Product::with('stocks')->findOrFail($productId);

            // 3. Variant and Pricing Resolution
            $unitPrice = (float)($product->discount_price > 0 ? $product->discount_price : $product->regular_price);
            $isVariant = 0;
            $variations = [];
            $matchedStock = null;

            if ($product->is_varient == 1 && $product->stocks->count() > 0) {
                $isVariant = 1;
                if ($selectedVariant) {
                    $matchedStock = $product->stocks->first(function ($item) use ($selectedVariant) {
                        return strcasecmp(trim($item->varient), trim($selectedVariant)) === 0;
                    });

                    // Order-independent variant matching fallback (e.g. Stitched-38 vs 38-Stitched)
                    if (!$matchedStock) {
                        $selParts = array_map('trim', explode('-', strtolower($selectedVariant)));
                        sort($selParts);
                        $matchedStock = $product->stocks->first(function ($item) use ($selParts) {
                            $stockParts = array_map('trim', explode('-', strtolower($item->varient)));
                            sort($stockParts);
                            return $stockParts === $selParts;
                        });
                    }
                }

                // If not matched or not provided, fallback to first stock variant
                if (!$matchedStock) {
                    $matchedStock = $product->stocks->first();
                }

                if ($matchedStock) {
                    if ($matchedStock->price > 0) {
                        $unitPrice = (float)$matchedStock->price;
                    } elseif ($product->discount_price > 0) {
                        $unitPrice = (float)$product->discount_price;
                    } else {
                        $unitPrice = (float)$product->regular_price;
                    }
                    $variantName = $matchedStock->varient;

                    $attributeName = 'Color';
                    $variations = [];
                    if (!empty($product->attributes)) {
                        $attrIds = is_array($product->attributes) ? $product->attributes : json_decode($product->attributes, true);
                        if (!empty($attrIds) && is_array($attrIds)) {
                            $parts = explode('-', $variantName);
                            if (count($parts) === count($attrIds)) {
                                foreach ($attrIds as $idx => $aId) {
                                    $attrModel = Attribute::find($aId);
                                    $variations[] = [
                                        'attribute_name'  => $attrModel ? $attrModel->name : 'Attribute ' . $aId,
                                        'attribute_value' => $parts[$idx] ?? '',
                                    ];
                                }

                                // Place Size at the end of variations
                                usort($variations, function ($a, $b) {
                                    $aIsSize = strtolower($a['attribute_name'] ?? '') === 'size';
                                    $bIsSize = strtolower($b['attribute_name'] ?? '') === 'size';
                                    if ($aIsSize && !$bIsSize) return 1;
                                    if (!$aIsSize && $bIsSize) return -1;
                                    return 0;
                                });
                            } else {
                                $firstAttr = Attribute::find($attrIds[0]);
                                if ($firstAttr) {
                                    $attributeName = $firstAttr->name;
                                }
                                $variations = [
                                    [
                                        'attribute_name'  => $attributeName,
                                        'attribute_value' => $variantName,
                                    ]
                                ];
                            }
                        }
                    }

                    if (empty($variations)) {
                        $variations = [
                            [
                                'attribute_name'  => $attributeName,
                                'attribute_value' => $variantName,
                            ]
                        ];
                    }
                }
            }

            $subTotal = $unitPrice * $quantity;

            // 4. Dynamic Shipping Charge from Backend Database
            $shippingCharge = 0.0;
            $shippingName = 'Standard Delivery';
            $shippingType = 2; // default outside Dhaka

            // Dhaka division check list
            $dhakaKeywords = ['ঢাকা', 'ঢাকা সিটি', 'Dhaka', 'dhaka', 'গাজীপুর', 'Gazipur', 'gazipur', 'নারায়ণগঞ্জ', 'Narayanganj', 'narayanganj'];
            $reqDistrict = trim((string)$request->district);

            if ($request->filled('shipping_id')) {
                $shipping = Shipping::where('id', $request->shipping_id)->where('status', 1)->first();
            } elseif ($request->filled('shipping_type')) {
                $shipping = Shipping::where('type', $request->shipping_type)->where('status', 1)->first();
            } else {
                $isDhaka = in_array($reqDistrict, $dhakaKeywords);
                $shippingType = $isDhaka ? 1 : 2;
                $shipping = Shipping::where('type', $shippingType)->where('status', 1)->first();
            }

            if ($shipping) {
                $shippingCharge = (float)$shipping->shipping_charge;
                $shippingName = $shipping->name;
                $shippingType = $shipping->type;
            } else {
                // Fallback default
                $shippingCharge = ($shippingType == 1) ? 80.0 : 150.0;
            }

            $grandTotal = $subTotal + $shippingCharge;

            // 5. User & Affiliate Determination
            $userId = null;
            $type = 2; // Guest by default

            if (Auth::check()) {
                $authUser = User::find(Auth::id());
                if ($authUser) {
                    $userId = $authUser->id;
                    $type = 1;
                }
            }

            if (!$userId) {
                $phone = trim((string)$request->phone);
                $existingCustomer = null;
                if (!empty($phone)) {
                    $existingCustomer = User::where('phone', $phone)->first();
                }

                if ($existingCustomer) {
                    $userId = $existingCustomer->id;
                } else {
                    $guestUser = User::where('id', 1)->first()
                        ?? User::where('role', 4)->first()
                        ?? User::first();

                    if (!$guestUser) {
                        $guestUser = User::create([
                            'name'     => $request->name ?? 'Guest User',
                            'email'    => 'guest_' . time() . rand(100, 999) . '@megamartbd.com',
                            'phone'    => !empty($phone) ? $phone : '01000000000',
                            'password' => bcrypt('12345678'),
                            'role'     => 4,
                            'status'   => 1,
                        ]);
                    }
                    $userId = $guestUser->id;
                }
            }

            $affiliateId = null;
            if (Session::has('affiliate_ref')) {
                $ref = Session::get('affiliate_ref');
                $affiliate = Affiliate::where('referral_code', $ref)->first();
                if ($affiliate) {
                    $affiliateId = $affiliate->id;
                }
            }

            // 6. District / Division Mapping
            $districtId = null;
            $divisionId = null;
            if ($reqDistrict) {
                $districtModel = District::where('district_name_bn', $reqDistrict)
                    ->orWhere('district_name_en', 'LIKE', '%' . $reqDistrict . '%')
                    ->first();
                if ($districtModel) {
                    $districtId = $districtModel->id;
                    $divisionId = $districtModel->division_id;
                }
            }

            // 7. Sequential Invoice Number
            $lastOrder = Order::orderBy('id', 'desc')->first();
            if ($lastOrder) {
                $invoiceNo = str_pad($lastOrder->id + 1, 7, '0', STR_PAD_LEFT);
            } else {
                $invoiceNo = "0000001";
            }

            // 8. Create Order
            $order = Order::create([
                'user_id'         => $userId,
                'affiliate_id'    => $affiliateId,
                'name'            => $request->name,
                'sub_total'       => $subTotal,
                'grand_total'     => $grandTotal,
                'shipping_charge' => $shippingCharge,
                'shipping_name'   => $shippingName,
                'shipping_type'   => $shippingType,
                'payment_method'  => $request->payment_option ?: 'cod',
                'payment_status'  => 0, // Pending / COD
                'invoice_no'      => $invoiceNo,
                'delivery_status' => 'pending',
                'phone'           => $request->phone,
                'email'           => $request->filled('email') ? trim($request->email) : null,
                'division_id'     => $divisionId,
                'district_id'     => $districtId,
                'upazilla_id'     => null,
                'address'         => trim($request->address),
                'comment'         => $request->comment,
                'coupon'          => null,
                'type'            => $type,
            ]);

            // 9. Create Order Detail
            $vendorId = $product->vendor_id ?: 0;
            OrderDetail::create([
                'order_id'     => $order->id,
                'invoice_no'   => $order->invoice_no,
                'product_id'   => $product->id,
                'vendor_id'    => $vendorId,
                'product_name' => $product->name_en ?: $product->name_bn,
                'is_varient'   => $isVariant,
                'variation'    => !empty($variations) ? json_encode($variations, JSON_UNESCAPED_UNICODE) : null,
                'qty'          => $quantity,
                'price'        => $unitPrice,
                'tax'          => 0,
                'created_at'   => Carbon::now(),
            ]);

            // 10. Update Product & Variant Stock
            if ($matchedStock) {
                $matchedStock->qty = max(0, $matchedStock->qty - $quantity);
                $matchedStock->save();
            }
            $product->stock_qty = max(0, $product->stock_qty - $quantity);
            $product->save();

            // 11. Account Ledger Entry
            $prevBalance = AccountLedger::orderBy('id', 'desc')->first();
            $currentBalance = $prevBalance ? $prevBalance->balance : 0;
            $newBalance = $currentBalance + $order->grand_total;

            AccountLedger::create([
                'account_head_id' => 2,
                'particulars'     => 'Landing Order ID: ' . $order->id . ' (Inv: ' . $order->invoice_no . ')',
                'credit'          => $order->grand_total,
                'order_id'        => $order->id,
                'balance'         => $newBalance,
                'type'            => 2,
            ]);

            DB::commit();

            try {
                $abandonedCheckoutCapture->markConverted($request->phone, $order->id);
            } catch (\Throwable $captureException) {
                Log::warning('Unable to mark landing checkout lead as converted.', ['exception' => $captureException->getMessage()]);
            }

            $redirectUrl = \Illuminate\Support\Facades\URL::signedRoute('checkout.invoice', ['order' => $order->id]);

            if ($request->ajax() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success'         => true,
                    'message'         => 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!',
                    'invoice_no'      => $order->invoice_no,
                    'order_id'        => $order->id,
                    'grand_total'     => $grandTotal,
                    'sub_total'       => $subTotal,
                    'shipping_charge' => $shippingCharge,
                    'redirect_url'    => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl)->with([
                'message'    => 'Your order has been placed successfully.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Landing Order Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'অর্ডার প্রক্রিয়া করার সময় ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন। (' . $e->getMessage() . ')',
                ], 422);
            }

            return back()->withInput()->with([
                'message'    => 'Order failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Display Turbo Fan Bundle Offer landing page with dynamic product & shipping info.
     */
    public function turboFanOffer()
    {
        // Fetch Product 206 with stocks, category, and brand
        $product = Product::with(['stocks', 'category', 'brand'])->find(206);
        if (!$product) {
            $product = Product::where('name_en', 'LIKE', '%Turbo%')
                ->orWhere('name_bn', 'LIKE', '%Turbo%')
                ->orWhere('slug', 'LIKE', '%turbo%')
                ->first() ?? Product::first();
        }

        // Fetch dynamic shipping rates from database
        $shipping_inside = Shipping::where('type', 1)->where('status', 1)->first();
        $shipping_outside = Shipping::where('type', 2)->where('status', 1)->first();
        $shippings = Shipping::where('status', 1)->get();

        return view('FrontEnd.landing.turbo-fan-offer', compact('product', 'shipping_inside', 'shipping_outside', 'shippings'));
    }

    /**
     * Dedicated Order Store method for Turbo Fan Bundle Offer landing page.
     * Tiered Bundle Pricing:
     * 1 Item: 649 BDT
     * 2 Items: 1049 BDT (Save 249 BDT)
     * 3 Items: 1249 BDT (Save 698 BDT)
     */
    public function turboFanOfferOrderStore(Request $request)
    {
        // 1. Validation
        $request->validate([
            'product_id'    => 'nullable|exists:products,id',
            'name'          => 'required|string|max:191',
            'phone'         => ['required', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'address'       => 'required|string|max:1000',
            'district'      => 'nullable|string|max:191',
            'shipping_type' => 'nullable|integer|in:1,2',
            'shipping_id'   => 'nullable|integer',
            'quantity'      => 'nullable|integer|min:1|max:100',
            'qty'           => 'nullable|integer|min:1|max:100',
            'color'         => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:2000',
        ]);

        $quantity = $request->filled('quantity') ? (int)$request->quantity : ((int)$request->qty ?: 3);
        $productId = $request->product_id ?: 206;
        $selectedColor = $request->color ?: 'white';

        try {
            DB::beginTransaction();

            // 2. Fetch Product (Fallback to 206 or first available)
            $product = Product::with('stocks')->find($productId);
            if (!$product) {
                $product = Product::with('stocks')->first();
            }

            // 3. Tiered Bundle Pricing Calculation
            $bundlePrices = [
                1 => 649,
                2 => 1049,
                3 => 1249,
            ];

            if (isset($bundlePrices[$quantity])) {
                $subTotal = (float)$bundlePrices[$quantity];
            } else {
                // If quantity > 3, calculate based on best tier rate or base price
                $subTotal = (float)(1249 + (($quantity - 3) * 416));
            }

            $unitPrice = round($subTotal / $quantity, 2);

            // 4. Color / Variant Resolution
            $colorNames = [
                'white' => 'White / সাদা',
                'mint'  => 'Mint / মিন্ট',
                'black' => 'Black / কালো',
            ];
            $variantDisplay = isset($colorNames[strtolower($selectedColor)]) 
                ? $colorNames[strtolower($selectedColor)] 
                : $selectedColor;

            $isVariant = 0;
            $variations = [];
            $matchedStock = null;

            if ($product && $product->is_varient == 1 && $product->stocks->count() > 0) {
                $isVariant = 1;
                $matchedStock = $product->stocks->first(function ($item) use ($selectedColor) {
                    return strcasecmp(trim($item->varient), trim($selectedColor)) === 0
                        || stripos($item->varient, $selectedColor) !== false;
                }) ?? $product->stocks->first();
            }

            $variations = [
                [
                    'attribute_name'  => 'Color / রঙ',
                    'attribute_value' => $variantDisplay,
                ]
            ];

            // 5. Dynamic Shipping Calculation (Inside/Outside Dhaka)
            $shippingType = (int)($request->shipping_type ?: 1); // 1: Dhaka, 2: Outside Dhaka
            $shipping = Shipping::where('type', $shippingType)->where('status', 1)->first();
            if ($shipping) {
                $shippingCharge = (float)$shipping->shipping_charge;
                $shippingName = $shipping->name;
            } else {
                $shippingCharge = ($shippingType == 1) ? 70.0 : 130.0;
                $shippingName = ($shippingType == 1) ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে';
            }

            $grandTotal = $subTotal + $shippingCharge;

            // 6. User / Customer Determination
            $userId = null;
            $type = 2; // Guest

            if (Auth::check()) {
                $authUser = User::find(Auth::id());
                if ($authUser) {
                    $userId = $authUser->id;
                    $type = 1;
                }
            }

            if (!$userId) {
                $phone = trim((string)$request->phone);
                $existingCustomer = null;
                if (!empty($phone)) {
                    $existingCustomer = User::where('phone', $phone)->first();
                }

                if ($existingCustomer) {
                    $userId = $existingCustomer->id;
                } else {
                    $guestUser = User::where('id', 1)->first()
                        ?? User::where('role', 4)->first()
                        ?? User::first();

                    if (!$guestUser) {
                        $guestUser = User::create([
                            'name'     => $request->name ?? 'Guest Customer',
                            'email'    => 'guest_' . time() . rand(100, 999) . '@megamartbd.com',
                            'phone'    => !empty($phone) ? $phone : '01000000000',
                            'password' => bcrypt('12345678'),
                            'role'     => 4,
                            'status'   => 1,
                        ]);
                    }
                    $userId = $guestUser->id;
                }
            }

            $affiliateId = null;
            if (Session::has('affiliate_ref')) {
                $ref = Session::get('affiliate_ref');
                $affiliate = Affiliate::where('referral_code', $ref)->first();
                if ($affiliate) {
                    $affiliateId = $affiliate->id;
                }
            }

            // 7. Sequential Invoice Number
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $invoiceNo = $lastOrder ? str_pad($lastOrder->id + 1, 7, '0', STR_PAD_LEFT) : "0000001";

            // 8. Create Order
            $order = Order::create([
                'user_id'         => $userId,
                'affiliate_id'    => $affiliateId,
                'name'            => $request->name,
                'sub_total'       => $subTotal,
                'grand_total'     => $grandTotal,
                'shipping_charge' => $shippingCharge,
                'shipping_name'   => $shippingName,
                'shipping_type'   => $shippingType,
                'payment_method'  => 'cod',
                'payment_status'  => 0, // Pending / COD
                'invoice_no'      => $invoiceNo,
                'delivery_status' => 'pending',
                'phone'           => $request->phone,
                'email'           => $request->email ?: null,
                'division_id'     => null,
                'district_id'     => null,
                'upazilla_id'     => null,
                'address'         => $request->address,
                'comment'         => $request->note ?: ('Bundle Offer: ' . $quantity . ' pcs (' . $variantDisplay . ')'),
                'coupon'          => null,
                'type'            => $type,
            ]);

            // 9. Create Order Detail
            $vendorId = ($product && $product->vendor_id) ? $product->vendor_id : 0;
            $productName = $product ? ($product->name_en ?: $product->name_bn) : 'Handheld Portable Turbo Fan (Bundle Offer)';

            OrderDetail::create([
                'order_id'     => $order->id,
                'invoice_no'   => $order->invoice_no,
                'product_id'   => $product ? $product->id : 0,
                'vendor_id'    => $vendorId,
                'product_name' => $productName . ' [' . $quantity . ' pcs Bundle - ' . $variantDisplay . ']',
                'is_varient'   => $isVariant,
                'variation'    => !empty($variations) ? json_encode($variations, JSON_UNESCAPED_UNICODE) : null,
                'qty'          => $quantity,
                'price'        => $unitPrice,
                'tax'          => 0,
                'created_at'   => Carbon::now(),
            ]);

            // 11. Stock update
            if ($product) {
                if ($matchedStock) {
                    $matchedStock->qty = max(0, $matchedStock->qty - $quantity);
                    $matchedStock->save();
                }
                $product->stock_qty = max(0, $product->stock_qty - $quantity);
                $product->save();
            }

            // 12. Account Ledger
            $prevBalance = AccountLedger::orderBy('id', 'desc')->first();
            $currentBalance = $prevBalance ? $prevBalance->balance : 0;
            $newBalance = $currentBalance + $order->grand_total;

            AccountLedger::create([
                'account_head_id' => 2,
                'particulars'     => 'Turbo Fan Offer Order ID: ' . $order->id . ' (Inv: ' . $order->invoice_no . ')',
                'credit'          => $order->grand_total,
                'order_id'        => $order->id,
                'balance'         => $newBalance,
                'type'            => 2,
            ]);

            DB::commit();

            $redirectUrl = \Illuminate\Support\Facades\URL::signedRoute('checkout.invoice', ['order' => $order->id]);

            if ($request->ajax() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success'         => true,
                    'message'         => 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে!',
                    'invoice_no'      => $order->invoice_no,
                    'order_id'        => $order->id,
                    'grand_total'     => $grandTotal,
                    'sub_total'       => $subTotal,
                    'shipping_charge' => $shippingCharge,
                    'redirect_url'    => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl)->with([
                'message'    => 'Your order has been placed successfully.',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Turbo Fan Offer Order Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'অর্ডার প্রক্রিয়া করার সময় ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন। (' . $e->getMessage() . ')',
                ], 422);
            }

            return back()->withInput()->with([
                'message'    => 'Order failed: ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingPage;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\MultiImg;
use App\Models\Shipping;
use Illuminate\Support\Str;
use Image;
use Session;

class LandingPageController extends Controller
{
    /**
     * Display a listing of landing pages.
     */
    public function index()
    {
        $landing_pages = LandingPage::with('product')->latest()->get();
        return view('backend.landing_page.index', compact('landing_pages'));
    }

    /**
     * Show form for creating a new landing page.
     */
    public function create()
    {
        $products = Product::where('status', 1)
            ->select('id', 'name_en', 'name_bn', 'product_thumbnail', 'regular_price', 'discount_price')
            ->latest()
            ->get();

        return view('backend.landing_page.create', compact('products'));
    }

    /**
     * AJAX endpoint to fetch product details for prefilling landing form.
     */
    public function ajaxProductDetails($id)
    {
        $product = Product::with(['stocks', 'multi_imgs', 'category', 'brand'])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Parse attributes and get names
        $attributesData = [];
        if (!empty($product->attributes)) {
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

                    $attributesData[] = [
                        'id' => $attrId,
                        'name' => $attrModel ? $attrModel->name : 'Attribute ' . $attrId,
                        'values' => $values,
                    ];
                }
            }
        }

        // Gallery images
        $gallery = [];
        if ($product->product_thumbnail) {
            $gallery[] = asset($product->product_thumbnail);
        }
        if ($product->multi_imgs && $product->multi_imgs->count() > 0) {
            foreach ($product->multi_imgs as $mImg) {
                $gallery[] = asset($mImg->photo_name);
            }
        }

        return response()->json([
            'success' => true,
            'id' => $product->id,
            'name_en' => $product->name_en,
            'name_bn' => $product->name_bn,
            'thumbnail' => $product->product_thumbnail ? asset($product->product_thumbnail) : null,
            'thumbnail_path' => $product->product_thumbnail,
            'regular_price' => $product->regular_price,
            'discount_price' => $product->discount_price,
            'description_en' => strip_tags($product->description_en ?? ''),
            'description_bn' => strip_tags($product->description_bn ?? ''),
            'gallery' => $gallery,
            'is_varient' => $product->is_varient,
            'attributes' => $attributesData,
            'stocks' => $product->stocks,
            'stock_qty' => $product->stock_qty,
        ]);
    }

    /**
     * Store newly created landing page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:landing_pages,slug',
        ]);

        $product = Product::findOrFail($request->product_id);

        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (LandingPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Slider images handling
        $sliderImages = [];
        if ($request->hasFile('slider_images')) {
            foreach ($request->file('slider_images') as $img) {
                $name_gen = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();
                $path = 'upload/landing/' . $name_gen;
                Image::make($img)->save(public_path($path));
                $sliderImages[] = $path;
            }
        } elseif ($request->filled('existing_slider_images')) {
            $sliderImages = is_array($request->existing_slider_images)
                ? $request->existing_slider_images
                : (json_decode($request->existing_slider_images, true) ?: []);
        }

        // Fallback to product images if none uploaded
        if (empty($sliderImages)) {
            if ($product->product_thumbnail) {
                $sliderImages[] = $product->product_thumbnail;
            }
            if ($product->multi_imgs) {
                foreach ($product->multi_imgs as $m) {
                    $sliderImages[] = $m->photo_name;
                }
            }
        }

        // Overview image handling
        $overviewImagePath = null;
        if ($request->hasFile('overview_image')) {
            $ovImg = $request->file('overview_image');
            $name_gen = hexdec(uniqid()) . '.' . $ovImg->getClientOriginalExtension();
            $path = 'upload/landing/' . $name_gen;
            Image::make($ovImg)->save(public_path($path));
            $overviewImagePath = $path;
        } elseif ($request->filled('existing_overview_image')) {
            $overviewImagePath = $request->existing_overview_image;
        } else {
            $overviewImagePath = $sliderImages[0] ?? $product->product_thumbnail;
        }

        // Process features
        $features = [];
        if ($request->filled('features')) {
            if (is_array($request->features)) {
                $features = array_values(array_filter($request->features));
            } else {
                $features = array_map('trim', explode("\n", str_replace("\r", "", $request->features)));
                $features = array_values(array_filter($features));
            }
        } else {
            $features = ['ক্যাশ অন ডেলিভারি', 'সারাদেশে ডেলিভারি', 'এক্সচেঞ্জ সুবিধা'];
        }

        // Process overview bullet points
        $overviewPoints = [];
        if ($request->filled('overview_points')) {
            if (is_array($request->overview_points)) {
                $overviewPoints = array_values(array_filter($request->overview_points));
            } else {
                $overviewPoints = array_map('trim', explode("\n", str_replace("\r", "", $request->overview_points)));
                $overviewPoints = array_values(array_filter($overviewPoints));
            }
        } else {
            $overviewPoints = [
                'Soft & comfortable premium fabric',
                'Elegant party and wedding design',
                'Neat finishing and premium appearance',
                'Available in multiple variations',
                'Cash on Delivery available',
            ];
        }

        // Process reviews
        $reviews = [];
        if ($request->filled('reviews') && is_array($request->reviews)) {
            foreach ($request->reviews as $rev) {
                if (!empty($rev['name']) && !empty($rev['comment'])) {
                    $reviews[] = [
                        'name' => $rev['name'],
                        'location' => $rev['location'] ?? 'ঢাকা',
                        'stars' => (int) ($rev['stars'] ?? 5),
                        'comment' => $rev['comment'],
                    ];
                }
            }
        }
        if (empty($reviews)) {
            $reviews = [
                ['name' => 'সাদিয়া রহমান', 'location' => 'ঢাকা', 'stars' => 5, 'comment' => 'কাপড়ের কোয়ালিটি খুব ভালো। ছবির মতোই সুন্দর পেয়েছি।'],
                ['name' => 'তানজিনা আক্তার', 'location' => 'চট্টগ্রাম', 'stars' => 5, 'comment' => 'ডেলিভারি দ্রুত ছিল এবং প্যাকেজিংও ভালো ছিল।'],
                ['name' => 'নুসরাত জাহান', 'location' => 'কুমিল্লা', 'stars' => 5, 'comment' => 'ডিজাইনটা সত্যিই খুব এলিগ্যান্ট ও প্রিমিয়াম।'],
                ['name' => 'মারিয়া ইসলাম', 'location' => 'নারায়ণগঞ্জ', 'stars' => 5, 'comment' => 'ফ্যাব্রিক আর ফিনিশিং দুইটাই খুব সুন্দর। আবারও অর্ডার করবো।'],
            ];
        }

        // Process FAQs
        $faqs = [];
        if ($request->filled('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                    ];
                }
            }
        }
        if (empty($faqs)) {
            $faqs = [
                ['question' => 'ডেলিভারি করতে কতদিন সময় লাগে?', 'answer' => 'ঢাকার ভিতরে সাধারণত ১–২ কর্মদিবস এবং ঢাকার বাইরে ২–৪ কর্মদিবস সময় লাগতে পারে।'],
                ['question' => 'ক্যাশ অন ডেলিভারি সুবিধা আছে কি?', 'answer' => 'হ্যাঁ, সারা বাংলাদেশে নিশ্চিত ক্যাশ অন ডেলিভারি সুবিধা রয়েছে।'],
                ['question' => 'সাইজ ও ভ্যারিয়েন্ট কীভাবে নির্বাচন করবো?', 'answer' => 'অর্ডারের সময় পছন্দের সাইজ এবং প্রয়োজনীয় ভ্যারিয়েন্ট সিলেক্ট করে এক ক্লিকেই অর্ডার করতে পারবেন।'],
                ['question' => 'প্রোডাক্ট এক্সচেঞ্জ সুবিধা পাওয়া যাবে কি?', 'answer' => 'হ্যাঁ, প্রোডাক্টে কোনো ত্রুটি থাকলে আমাদের এক্সচেঞ্জ পলিসি অনুযায়ী দ্রুত এক্সচেঞ্জ করা যাবে।'],
                ['question' => 'ছবির মতো হুবহু আসল প্রোডাক্ট পাবো?', 'answer' => 'আমরা আসল প্রোডাক্টের ছবি প্রদর্শনের সর্বোচ্চ নিশ্চয়তা দিয়ে থাকি। ১০০% খাঁটি প্রোডাক্ট পাবেন।'],
            ];
        }

        // Calculate discount text if not given
        $regPrice = $request->filled('regular_price') ? (float) $request->regular_price : (float) $product->regular_price;
        $salePrice = $request->filled('sale_price') ? (float) $request->sale_price : (float) ($product->discount_price > 0 ? $product->discount_price : $product->regular_price);
        $discountText = $request->discount_text;
        if (empty($discountText) && $regPrice > $salePrice && $regPrice > 0) {
            $pct = round((($regPrice - $salePrice) / $regPrice) * 100);
            $discountText = $pct . '% ছাড়';
        }

        $shippingDhaka = Shipping::where('type', 1)->value('shipping_charge') ?? 80.00;
        // Process steps and guarantee into about_cards
        $steps = [];
        if ($request->filled('steps') && is_array($request->steps)) {
            foreach ($request->steps as $stp) {
                if (!empty($stp['title'])) {
                    $steps[] = [
                        'title' => $stp['title'],
                        'desc' => $stp['desc'] ?? '',
                    ];
                }
            }
        }
        if (empty($steps)) {
            $steps = [
                ['title' => 'ফর্ম পূরণ করুন', 'desc' => 'নাম, ফোন, ঠিকানা — ৩০ সেকেন্ড'],
                ['title' => 'কনফার্মেশন কল', 'desc' => 'আমরা কল দিয়ে নিশ্চিত করব'],
                ['title' => 'হাতে পেয়ে টাকা', 'desc' => 'খুলে দেখে তারপর পেমেন্ট'],
            ];
        }

        $aboutCards = [
            'steps' => $steps,
            'guarantee_title' => $request->guarantee_title ?: 'ঝুঁকিটা আমাদের, আপনার না',
            'guarantee_desc' => $request->guarantee_desc ?: 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে দেখুন। ছবির সাথে না মিললে বা কোনো সমস্যা থাকলে গ্রহণ করবেন না — টাকা দেওয়ার কোনো প্রশ্নই আসে না।',
        ];

        LandingPage::create([
            'product_id' => $product->id,
            'title' => $request->title,
            'slug' => $slug,
            'sub_title' => $request->sub_title ?? ($product->name_bn ?: $product->name_en),
            'theme_color' => $request->theme_color ?: '#000000',
            'btn_color' => $request->btn_color ?: '#E0B04A',
            'btn_text_color' => $request->btn_text_color ?: '#2B1C06',
            'bg_color' => $request->bg_color ?: '#1F0A0F',
            'top_notice' => $request->top_notice ?: '✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন',
            'hero_tag' => $request->hero_tag ?: 'PREMIUM COLLECTION',
            'slider_images' => $sliderImages,
            'video_url' => $request->video_url,
            'regular_price' => $regPrice,
            'sale_price' => $salePrice,
            'discount_text' => $discountText,
            'stock_text' => $request->stock_text ?: 'বর্তমান স্টক: সীমিত',
            'features' => $features,
            'overview_title' => $request->overview_title ?: 'পণ্যের বিশেষ বৈশিষ্ট্য',
            'overview_sub_title' => $request->overview_sub_title ?: 'প্রোডাক্ট সম্পর্কে দরকারি তথ্য এক জায়গায়।',
            'overview_image' => $overviewImagePath,
            'overview_text' => $request->overview_text ?: ($product->description_en ?: $product->description_bn),
            'overview_points' => $overviewPoints,
            'about_cards' => $aboutCards,
            'reviews' => $reviews,
            'faqs' => $faqs,
            'delivery_charge_dhaka' => $request->filled('delivery_charge_dhaka') ? $request->delivery_charge_dhaka : $shippingDhaka,
            'delivery_charge_outside' => $request->filled('delivery_charge_outside') ? $request->delivery_charge_outside : $shippingOutside,
            'status' => $request->has('status') ? (int) $request->status : 1,
        ]);

        Session::flash('success', 'ল্যান্ডিং পেজ সফলভাবে তৈরি হয়েছে!');
        return redirect()->route('landing.page.index');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $landingPage = LandingPage::with('product.stocks')->findOrFail($id);
        $products = Product::where('status', 1)
            ->select('id', 'name_en', 'name_bn', 'product_thumbnail', 'regular_price', 'discount_price')
            ->latest()
            ->get();

        return view('backend.landing_page.edit', compact('landingPage', 'products'));
    }

    /**
     * Update landing page.
     */
    public function update(Request $request, $id)
    {
        $landingPage = LandingPage::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:landing_pages,slug,' . $landingPage->id,
        ]);

        $product = Product::findOrFail($request->product_id);

        // Slider images handling
        $sliderImages = $landingPage->slider_images ?: [];
        if ($request->hasFile('slider_images')) {
            $newImages = [];
            foreach ($request->file('slider_images') as $img) {
                $name_gen = hexdec(uniqid()) . '.' . $img->getClientOriginalExtension();
                $path = 'upload/landing/' . $name_gen;
                Image::make($img)->save(public_path($path));
                $newImages[] = $path;
            }
            if ($request->filled('keep_existing_slider') && $request->keep_existing_slider == '1') {
                $sliderImages = array_merge($sliderImages, $newImages);
            } else {
                $sliderImages = $newImages;
            }
        }

        // Overview image handling
        $overviewImagePath = $landingPage->overview_image;
        if ($request->hasFile('overview_image')) {
            $ovImg = $request->file('overview_image');
            $name_gen = hexdec(uniqid()) . '.' . $ovImg->getClientOriginalExtension();
            $path = 'upload/landing/' . $name_gen;
            Image::make($ovImg)->save(public_path($path));
            $overviewImagePath = $path;
        }

        // Process features
        $features = [];
        if ($request->filled('features')) {
            if (is_array($request->features)) {
                $features = array_values(array_filter($request->features));
            } else {
                $features = array_map('trim', explode("\n", str_replace("\r", "", $request->features)));
                $features = array_values(array_filter($features));
            }
        } else {
            $features = $landingPage->features ?: ['ক্যাশ অন ডেলিভারি', 'সারাদেশে ডেলিভারি', 'এক্সচেঞ্জ সুবিধা'];
        }

        // Process overview bullet points
        $overviewPoints = [];
        if ($request->filled('overview_points')) {
            if (is_array($request->overview_points)) {
                $overviewPoints = array_values(array_filter($request->overview_points));
            } else {
                $overviewPoints = array_map('trim', explode("\n", str_replace("\r", "", $request->overview_points)));
                $overviewPoints = array_values(array_filter($overviewPoints));
            }
        } else {
            $overviewPoints = $landingPage->overview_points ?: [];
        }

        // Process reviews
        $reviews = [];
        if ($request->filled('reviews') && is_array($request->reviews)) {
            foreach ($request->reviews as $rev) {
                if (!empty($rev['name']) && !empty($rev['comment'])) {
                    $reviews[] = [
                        'name' => $rev['name'],
                        'location' => $rev['location'] ?? 'ঢাকা',
                        'stars' => (int) ($rev['stars'] ?? 5),
                        'comment' => $rev['comment'],
                    ];
                }
            }
        }
        if (empty($reviews)) {
            $reviews = $landingPage->reviews ?: [];
        }

        // Process FAQs
        $faqs = [];
        if ($request->filled('faqs') && is_array($request->faqs)) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                    ];
                }
            }
        }
        if (empty($faqs)) {
            $faqs = $landingPage->faqs ?: [];
        }

        $regPrice = $request->filled('regular_price') ? (float) $request->regular_price : (float) $product->regular_price;
        $salePrice = $request->filled('sale_price') ? (float) $request->sale_price : (float) ($product->discount_price > 0 ? $product->discount_price : $product->regular_price);
        $discountText = $request->discount_text;
        if (empty($discountText) && $regPrice > $salePrice && $regPrice > 0) {
            $pct = round((($regPrice - $salePrice) / $regPrice) * 100);
            $discountText = $pct . '% ছাড়';
        }

        $shippingDhaka = Shipping::where('type', 1)->value('shipping_charge') ?? 80.00;
        $shippingOutside = Shipping::where('type', 2)->value('shipping_charge') ?? 150.00;

        // Process steps and guarantee into about_cards
        $steps = [];
        if ($request->filled('steps') && is_array($request->steps)) {
            foreach ($request->steps as $stp) {
                if (!empty($stp['title'])) {
                    $steps[] = [
                        'title' => $stp['title'],
                        'desc' => $stp['desc'] ?? '',
                    ];
                }
            }
        }
        if (empty($steps)) {
            $existingAbout = is_array($landingPage->about_cards) ? $landingPage->about_cards : [];
            $steps = $existingAbout['steps'] ?? [
                ['title' => 'ফর্ম পূরণ করুন', 'desc' => 'নাম, ফোন, ঠিকানা — ৩০ সেকেন্ড'],
                ['title' => 'কনফার্মেশন কল', 'desc' => 'আমরা কল দিয়ে নিশ্চিত করব'],
                ['title' => 'হাতে পেয়ে টাকা', 'desc' => 'খুলে দেখে তারপর পেমেন্ট'],
            ];
        }

        $aboutCards = [
            'steps' => $steps,
            'guarantee_title' => $request->guarantee_title ?: 'ঝুঁকিটা আমাদের, আপনার না',
            'guarantee_desc' => $request->guarantee_desc ?: 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে দেখুন। ছবির সাথে না মিললে বা কোনো সমস্যা থাকলে গ্রহণ করবেন না — টাকা দেওয়ার কোনো প্রশ্নই আসে না।',
        ];

        $landingPage->update([
            'product_id' => $product->id,
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'sub_title' => $request->sub_title,
            'theme_color' => $request->theme_color ?: '#000000',
            'btn_color' => $request->btn_color ?: '#E0B04A',
            'btn_text_color' => $request->btn_text_color ?: '#2B1C06',
            'bg_color' => $request->bg_color ?: '#1F0A0F',
            'top_notice' => $request->top_notice ?: '✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন',
            'hero_tag' => $request->hero_tag,
            'slider_images' => $sliderImages,
            'video_url' => $request->video_url,
            'regular_price' => $regPrice,
            'sale_price' => $salePrice,
            'discount_text' => $discountText,
            'stock_text' => $request->stock_text ?: 'বর্তমান স্টক: সীমিত',
            'features' => $features,
            'overview_title' => $request->overview_title ?: 'পণ্যের বিশেষ বৈশিষ্ট্য',
            'overview_sub_title' => $request->overview_sub_title,
            'overview_image' => $overviewImagePath,
            'overview_text' => $request->overview_text,
            'overview_points' => $overviewPoints,
            'about_cards' => $aboutCards,
            'reviews' => $reviews,
            'faqs' => $faqs,
            'delivery_charge_dhaka' => $request->filled('delivery_charge_dhaka') ? $request->delivery_charge_dhaka : ($landingPage->delivery_charge_dhaka ?: $shippingDhaka),
            'delivery_charge_outside' => $request->filled('delivery_charge_outside') ? $request->delivery_charge_outside : ($landingPage->delivery_charge_outside ?: $shippingOutside),
            'status' => $request->has('status') ? (int) $request->status : 0,
        ]);

        Session::flash('success', 'ল্যান্ডিং পেজ সফলভাবে আপডেট করা হয়েছে!');
        return redirect()->route('landing.page.index');
    }

    /**
     * Delete landing page.
     */
    public function destroy($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        $landingPage->delete();

        Session::flash('success', 'ল্যান্ডিং পেজ সফলভাবে মুছে ফেলা হয়েছে!');
        return redirect()->route('landing.page.index');
    }

    /**
     * Toggle status.
     */
    public function changeStatus($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        $landingPage->status = $landingPage->status == 1 ? 0 : 1;
        $landingPage->save();

        Session::flash('success', 'স্ট্যাটাস সফলভাবে পরিবর্তিত হয়েছে!');
        return redirect()->back();
    }
}

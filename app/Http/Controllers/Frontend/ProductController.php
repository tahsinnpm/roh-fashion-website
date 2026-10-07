<?php

namespace App\Http\Controllers\Frontend;

use DateTime;
use App\Models\Brand;
use App\Models\Review;
use App\Models\Product;
use App\Models\Campaing;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductStock;
use App\Models\DynamicSection;
use App\Utility\CategoryUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AttributeValue;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activeSection = null;
        $activeCategory = null;
        $activeBrand = null;

        // Base query with eager-loaded stocks and relations
        $query = Product::where('status', 1)->with(['stocks', 'category', 'brand']);

        // 1. Dynamic Section filter (?section=slug or ?section_id=id)
        if ($request->filled('section')) {
            $sectionParam = $request->section;
            $activeSection = DynamicSection::where('slug', $sectionParam)
                ->orWhere('id', $sectionParam)
                ->first();

            if ($activeSection) {
                $sectionProductIds = $activeSection->products()->pluck('products.id');
                $query->whereIn('id', $sectionProductIds);
            }
        } elseif ($request->filled('section_id')) {
            $activeSection = DynamicSection::find($request->section_id);
            if ($activeSection) {
                $sectionProductIds = $activeSection->products()->pluck('products.id');
                $query->whereIn('id', $sectionProductIds);
            }
        }

        // 2. Featured filter (?featured=1 or ?filter=featured)
        if ($request->boolean('featured') || $request->filter === 'featured') {
            $query->where('is_featured', 1);
        }

        // 3. Category filter (?category=slug or ?category[]=slug/id/name)
        if ($request->filled('category')) {
            $rawCats = (array) $request->category;
            $catIds = [];
            foreach ($rawCats as $catVal) {
                if (is_numeric($catVal)) {
                    $catIds[] = (int) $catVal;
                    $catIds = array_merge($catIds, CategoryUtility::children_ids((int) $catVal));
                } else {
                    $foundCat = Category::where('slug', $catVal)->orWhere('name_en', $catVal)->first();
                    if ($foundCat) {
                        $catIds[] = $foundCat->id;
                        $catIds = array_merge($catIds, CategoryUtility::children_ids($foundCat->id));
                        if (!$activeCategory && count($rawCats) === 1) {
                            $activeCategory = $foundCat;
                        }
                    }
                }
            }
            if (!empty($catIds)) {
                $query->whereIn('category_id', array_unique($catIds));
            }
        }

        // 4. Brand filter (?brand=slug or ?brand[]=slug/id/name)
        if ($request->filled('brand')) {
            $rawBrands = (array) $request->brand;
            $brandIds = [];
            foreach ($rawBrands as $brandVal) {
                if (is_numeric($brandVal)) {
                    $brandIds[] = (int) $brandVal;
                } else {
                    $foundBrand = Brand::where('slug', $brandVal)->orWhere('name_en', $brandVal)->first();
                    if ($foundBrand) {
                        $brandIds[] = $foundBrand->id;
                        if (!$activeBrand && count($rawBrands) === 1) {
                            $activeBrand = $foundBrand;
                        }
                    }
                }
            }
            if (!empty($brandIds)) {
                $query->whereIn('brand_id', array_unique($brandIds));
            }
        }

        // 5. In-Stock filter (?stock=1)
        if ($request->filled('stock') && (int) $request->stock === 1) {
            $query->where('stock_qty', '>', 0);
        }

        // 6. Price range filter (?min_price=100&max_price=2000)
        if ($request->filled('min_price') && (float) $request->min_price > 0) {
            $query->where('regular_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price') && (float) $request->max_price > 0) {
            $query->where('regular_price', '<=', (float) $request->max_price);
        }

        // 7. Search query (?search=term or ?q=term)
        $searchKey = $request->input('search', $request->input('q'));
        if (!empty($searchKey)) {
            $searchTerm = trim($searchKey);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_en', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name_bn', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('product_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 8. Sorting & Filter Presets (New Arrivals, Best Sellers)
        $filterPreset = $request->input('filter');
        $sort_by = $request->input('sort_by', $request->input('sort', 'newest'));

        if ($filterPreset === 'new_arrivals') {
            $sort_by = 'newest';
        } elseif ($filterPreset === 'best_sellers') {
            $sort_by = 'best_sellers';
        }

        switch ($sort_by) {
            case 'newest':
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
                break;
            case 'price-asc':
            case 'price_low_high':
                $query->orderBy('regular_price', 'asc');
                break;
            case 'price-desc':
            case 'price_high_low':
                $query->orderBy('regular_price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name_en', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name_en', 'desc');
                break;
            case 'best_sellers':
            case 'popular':
                $query->withCount([
                    'order_details as total_sales_qty' => function ($q) {
                        $q->select(DB::raw('COALESCE(SUM(qty), 0)'));
                    }
                ])
                ->orderByDesc('total_sales_qty')
                ->orderByDesc('id');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Pagination
        $products = $query->paginate(20)->appends($request->query());

        // Sidebar categories & brands with counts
        $categories = Category::where('status', 1)
            ->where(function($q) {
                $q->where('parent_id', 0)->orWhereNull('parent_id');
            })
            ->with(['sub_categories' => function($q) {
                $q->where('status', 1)->withCount(['products' => function($pq) {
                    $pq->where('status', 1);
                }]);
            }])
            ->withCount(['products' => function($q) {
                $q->where('status', 1);
            }])
            ->orderBy('name_en', 'asc')
            ->get();

        $brands = Brand::where('status', 1)
            ->withCount(['products' => function($q) {
                $q->where('status', 1);
            }])
            ->orderBy('name_en', 'asc')
            ->get();

        $dynamicSections = DynamicSection::where('status', 1)->ordered()->get();
        $inStockCount = Product::where('status', 1)->where('stock_qty', '>', 0)->count();

        $catalogMinPrice = (float) Product::where('status', 1)->min('regular_price') ?: 0;
        $catalogMaxPrice = (float) Product::where('status', 1)->max('regular_price') ?: 5000;

        return view('FrontEnd.product.shop', compact(
            'products',
            'categories',
            'brands',
            'dynamicSections',
            'activeSection',
            'activeCategory',
            'activeBrand',
            'inStockCount',
            'catalogMinPrice',
            'catalogMaxPrice',
            'sort_by'
        ));
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
     * @param  int  $varient
     * @return \Illuminate\Http\Response
     */
    public function getVarient($id, $varient)
    {
        $stock = ProductStock::where('product_id', $id)->where('varient', $varient)->first();
        if($stock){
            return $stock;
        }else{
            return null;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function campaignProducts()
    {
        $campaign = Campaing::where('status', 1)->where('is_featured', 1)->orderBy('id', 'desc')->first();
        // return view('FrontEnd.campaign.index', compact('campaign'));

        // $campaign = Campaing::where('status', 1)->where('is_featured', 1)->orderBy('id', 'desc')->first();

    if ($campaign && $this->isCampaignActive($campaign)) {
        return view('FrontEnd.campaign.index', compact('campaign'));
    } else {
        return abort(404);
    }
    }

    protected function isCampaignActive($campaign)
    {
    $flashEnd = date_create($campaign->flash_end);
    $now = new DateTime();

    return $flashEnd > $now;
    }

    // ProductController.php

public function getReviewsAjax(Request $request, $slug)
{
    $product = Product::where('slug', $slug)->firstOrFail();

    $query = Review::where('product_id', $product->id)
        ->where('status', 1)
        ->where('verified_purchase', 1);

    // Filter by star rating
    if ($request->filled('filter') && $request->filter != 'all') {
        $query->where('rating', $request->filter);
    }

    // Sort
    switch ($request->get('sort')) {
        case 'recent':
            $query->orderBy('created_at', 'desc');
            break;
        case 'rating_desc':
            $query->orderBy('rating', 'desc');
            break;
        case 'rating_asc':
            $query->orderBy('rating', 'asc');
            break;
        default:
            // relevance or default ordering by ID desc
            $query->orderBy('id', 'desc');
            break;
    }

    $reviews = $query->get();

    // Return JSON response with rendered HTML partial view
    $html = view('FrontEnd.product.partials.reviews_list', compact('reviews'))->render();

    return response()->json(['html' => $html]);
}

}

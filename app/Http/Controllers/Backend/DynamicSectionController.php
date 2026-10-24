<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicSection;
use App\Models\DynamicSectionProduct;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Session;

class DynamicSectionController extends Controller
{
    /**
     * Display a listing of dynamic sections.
     */
    public function index()
    {
        $sections = DynamicSection::withCount('products')->orderBy('position', 'asc')->get();
        return view('backend.dynamic_section.index', compact('sections'));
    }

    /**
     * Show form for creating a dynamic section.
     */
    public function create()
    {
        $categories = Category::where('type', 1)->orderBy('name_en')->get(['id', 'name_en', 'name_bn']);
        $products = Product::where('status', 1)
            ->select('id', 'category_id', 'name_en', 'name_bn', 'product_thumbnail', 'regular_price')
            ->latest()
            ->get();

        $next_position = (DynamicSection::max('position') ?? 0) + 1;

        return view('backend.dynamic_section.create', compact('categories', 'products', 'next_position'));
    }

    /**
     * Store a newly created dynamic section.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:dynamic_sections,slug',
            'position' => 'nullable|integer',
            'category_id' => 'nullable|integer|exists:categories,id',
            'products' => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
        ]);

        $position = $request->position;
        if ($position === null || $position === '') {
            $position = (DynamicSection::max('position') ?? 0) + 1;
        }

        $categoryId = $request->input('category_id');
        $productIds = $categoryId
            ? Product::where('category_id', $categoryId)->where('status', 1)->orderBy('id')->pluck('id')
            : collect($request->input('products', []))->unique()->values();

        DB::transaction(function () use ($request, $position, $categoryId, $productIds) {
            $section = DynamicSection::create([
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'slug' => Str::slug($request->slug),
                'position' => $position,
                'category_id' => $categoryId,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            foreach ($productIds as $index => $productId) {
                $pos = isset($request->product_positions[$productId]) ? (int) $request->product_positions[$productId] : ($index + 1);
                DynamicSectionProduct::create([
                    'dynamic_section_id' => $section->id,
                    'product_id' => $productId,
                    'position' => $pos,
                ]);
            }
        });

        Session::flash('success', 'Dynamic section created successfully.');
        return redirect()->route('dynamic-section.index');
    }

    /**
     * Show form for editing a dynamic section.
     */
    public function edit($id)
    {
        $section = DynamicSection::with(['section_products' => function($q) {
            $q->orderBy('position', 'asc')->with('product');
        }])->findOrFail($id);

        $selectedProductIds = $section->section_products->pluck('product_id')->toArray();

        $categories = Category::where('type', 1)->orderBy('name_en')->get(['id', 'name_en', 'name_bn']);
        $products = Product::where('status', 1)
            ->select('id', 'category_id', 'name_en', 'name_bn', 'product_thumbnail', 'regular_price')
            ->latest()
            ->get();

        return view('backend.dynamic_section.edit', compact('section', 'categories', 'products', 'selectedProductIds'));
    }

    /**
     * Update an existing dynamic section.
     */
    public function update(Request $request, $id)
    {
        $section = DynamicSection::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:dynamic_sections,slug,' . $id,
            'position' => 'nullable|integer',
            'category_id' => 'nullable|integer|exists:categories,id',
            'products' => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
        ]);

        $categoryId = $request->input('category_id');
        $productIds = $categoryId
            ? Product::where('category_id', $categoryId)->where('status', 1)->orderBy('id')->pluck('id')
            : collect($request->input('products', []))->unique()->values();

        DB::transaction(function () use ($request, $section, $categoryId, $productIds) {
            $section->update([
                'title' => $request->title,
                'title_bn' => $request->title_bn,
                'slug' => Str::slug($request->slug),
                'position' => $request->position ?? $section->position,
                'category_id' => $categoryId,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            DynamicSectionProduct::where('dynamic_section_id', $section->id)->delete();

            foreach ($productIds as $index => $productId) {
                $pos = isset($request->product_positions[$productId]) ? (int) $request->product_positions[$productId] : ($index + 1);
                DynamicSectionProduct::create([
                    'dynamic_section_id' => $section->id,
                    'product_id' => $productId,
                    'position' => $pos,
                ]);
            }
        });

        Session::flash('success', 'Dynamic section updated successfully.');
        return redirect()->route('dynamic-section.index');
    }

    /**
     * Delete dynamic section.
     */
    public function destroy($id)
    {
        $section = DynamicSection::findOrFail($id);
        $section->delete();

        Session::flash('success', 'Dynamic section deleted successfully.');
        return redirect()->route('dynamic-section.index');
    }

    /**
     * Toggle status.
     */
    public function changeStatus(Request $request, $id)
    {
        $section = DynamicSection::findOrFail($id);
        $section->status = $section->status == 1 ? 0 : 1;
        $section->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $section->status,
                'message' => 'Status updated successfully!'
            ]);
        }

        Session::flash('success', 'Status updated successfully.');
        return back();
    }

    /**
     * AJAX drag-and-drop reordering for sections.
     */
    public function updatePositions(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $id) {
            DynamicSection::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Section positions updated successfully!'
        ]);
    }

    /**
     * AJAX drag-and-drop reordering for products inside a section.
     */
    public function updateProductPositions(Request $request, $id)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $productId) {
            DynamicSectionProduct::where('dynamic_section_id', $id)
                ->where('product_id', $productId)
                ->update(['position' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product positions updated successfully!'
        ]);
    }

    /**
     * AJAX endpoint to fetch product row details for adding to drag-drop table.
     */
    public function ajaxGetProduct(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $thumb = $product->product_thumbnail;
        if ($thumb) {
            $thumb = preg_replace('/^(\/?public\/)+/', '', $thumb);
            $thumb = ltrim($thumb, '/');
        }
        if (!$thumb || !file_exists(public_path($thumb))) {
            $thumb = 'upload/product-default.jpg';
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_bn' => $product->name_bn,
                'thumbnail' => asset($thumb),
                'regular_price' => $product->regular_price,
            ]
        ]);
    }
}

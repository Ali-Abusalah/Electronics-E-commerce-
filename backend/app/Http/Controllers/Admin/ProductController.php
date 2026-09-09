<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->select('products.*', 'categories.name as category_name', 'brands.name as brand_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('products.slug', 'like', "%{$search}%")
                  ->orWhere('products.description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->category_id);
        }

        $products = $query->orderBy('products.created_at', 'desc')->paginate(15);
        $categories = DB::table('categories')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = DB::table('categories')->get();
        $brands = DB::table('brands')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'description' => 'nullable|string',
            'overview' => 'nullable|string',
            'features' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $image = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $image = 'uploads/products/' . $filename;
        }

        DB::table('products')->insert([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $image,
            'description' => $validated['description'] ?? null,
            'overview' => $validated['overview'] ?? null,
            'features' => $validated['features'] ?? null,
            'is_active' => true,
            'status' => $validated['status'] ?? 'active',
            'rating' => 0,
            'reviews' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }
        $categories = DB::table('categories')->get();
        $brands = DB::table('brands')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'description' => 'nullable|string',
            'overview' => 'nullable|string',
            'features' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $image = $product->image;

        if ($request->hasFile('image_file')) {
            if ($product->image && str_starts_with($product->image, 'uploads/')) {
                $old = public_path($product->image);
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $image = 'uploads/products/' . $filename;
        }

        DB::table('products')->where('id', $id)->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $image,
            'description' => $validated['description'] ?? null,
            'overview' => $validated['overview'] ?? null,
            'features' => $validated['features'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        DB::table('products')->where('id', $id)->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}

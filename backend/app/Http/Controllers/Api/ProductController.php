<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand'])
            ->where('is_active', true);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($qb) use ($search) {
                        $qb->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        if ($brands = $request->query('brand')) {
            $brandNames = explode(',', $brands);
            $query->whereHas('brand', function ($q) use ($brandNames) {
                $q->whereIn('name', $brandNames);
            });
        }

        if ($maxPrice = $request->query('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        if ($request->query('in_stock') == '1') {
            $query->where('stock', '>', 0);
        }

        if ($request->query('out_of_stock') == '1') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->get();

        $transformed = $products->map(function ($product) {
            $image = $product->image;
            if ($image && !str_starts_with($image, 'http')) {
                $image = url($image);
            }
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
                'price' => (float) $product->price,
                'image' => $image,
                'alt' => $product->alt,
                'description' => $product->description,
                'overview' => $product->overview ?? null,
                'features' => $product->features ?? [],
                'stock' => (int) $product->stock,
                'rating' => (float) $product->rating,
                'reviews' => (int) $product->reviews,
            ];
        });

        return response()->json($transformed);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'brand']);

        $image = $product->image;
        if ($image && !str_starts_with($image, 'http')) {
            $image = url('public/' . ltrim($image, '/'));
        }
        $transformed = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category?->name,
            'brand' => $product->brand?->name,
            'price' => (float) $product->price,
            'image' => $image,
            'alt' => $product->alt,
            'description' => $product->description,
            'overview' => $product->overview ?? null,
            'features' => $product->features ?? [],
            'stock' => (int) $product->stock,
            'rating' => (float) $product->rating,
            'reviews' => (int) $product->reviews,
        ];

        return response()->json($transformed);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = Review::where('product_id', $product->id)
            ->with('user:name')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $review = $existing;
        } else {
            $review = Review::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        }

        $this->updateProductRating($product);

        return response()->json($review->load('user'), 201);
    }

    public function destroy(Product $product, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();
        $this->updateProductRating($product);

        return response()->json(['message' => 'Deleted']);
    }

    private function updateProductRating(Product $product): void
    {
        $stats = Review::where('product_id', $product->id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->first();

        $product->update([
            'rating' => round($stats->avg_rating ?? 0, 1),
            'reviews' => $stats->review_count ?? 0,
        ]);
    }
}

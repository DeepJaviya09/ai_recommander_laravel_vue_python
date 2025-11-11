<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Recommendation;

class RecommendationController extends Controller
{
    // public function index(Request $request)
    // {
    //     $products = Product::inRandomOrder()->limit(5)->get();

    //     Recommendation::updateOrCreate(
    //         ['user_id' => $request->user()->id],
    //         ['recommended_products' => $products->pluck('id')->values()->all()]
    //     );

    //     return response()->json(['products' => $products]);
    // }

    public function index(Request $request)
    {
        // 🧩 Get the authenticated user (via Sanctum)
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 🧠 Find user's recommendations
        $recommendation = Recommendation::where('user_id', $user->id)->first();

        if (!$recommendation) {
            return response()->json(['message' => 'No recommendations found.'], 200);
        }

        // 🎯 Decode product IDs safely
        $productIds = is_string($recommendation->recommended_products)
            ? json_decode($recommendation->recommended_products, true)
            : $recommendation->recommended_products;

        if (!is_array($productIds)) {
            $productIds = [];
        }

        // 🛒 Fetch full product details
        $products = Product::whereIn('id', $productIds)
            ->with('category') // optional
            ->get();

        return response()->json([
            'user_id' => $user->id,
            'recommended_products' => $products
        ]);
    }

}

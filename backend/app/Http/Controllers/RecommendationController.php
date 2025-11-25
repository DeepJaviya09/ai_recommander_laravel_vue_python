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

    /**
     * Get AI-powered personalized recommendations for a user
     * Calls the Python FastAPI service
     * 
     * @param Request $request
     * @param int $id User ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendForUser(Request $request, $id)
    {
        // Validate user access (users can only get their own recommendations, or admin can get any)
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user is requesting their own recommendations or is admin
        if ($user->id != $id && $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $limit = $request->query('limit', 10);
        $aiServiceUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
        
        try {
            // Call Python AI service
            $url = "{$aiServiceUrl}/recommend/user/{$id}?limit={$limit}";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json([
                    'error' => 'AI service connection failed',
                    'message' => $curlError
                ], 503);
            }

            if ($httpCode !== 200) {
                return response()->json([
                    'error' => 'AI service error',
                    'http_code' => $httpCode,
                    'response' => $response
                ], 503);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid response from AI service',
                    'response' => $response
                ], 500);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch recommendations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}

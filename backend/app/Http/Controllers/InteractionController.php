<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class InteractionController extends Controller
{
    public function visit(Request $request, $id)
    {
        $user = $request->user();
        
        // Don't log visits for admin users
        if ($user->role === 'admin') {
            return response()->json(['ok' => true, 'message' => 'Visit not logged for admin']);
        }

        $product = Product::findOrFail($id);
        DB::table('visited_products')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'visited_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }

    public function buy(Request $request, $id)
    {
        $user = $request->user();
        
        // Don't allow purchases for admin users
        if ($user->role === 'admin') {
            return response()->json([
                'ok' => false,
                'message' => 'Admin users cannot purchase products'
            ], 403);
        }

        $product = Product::findOrFail($id);
        DB::table('purchased_products')->insert([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'purchased_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    }
}

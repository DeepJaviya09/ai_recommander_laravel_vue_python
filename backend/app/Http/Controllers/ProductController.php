<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{

    public function allProducts()
    {
        return Product::with('category')->get();
    }
    
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Support filtering by category_id or category name
        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        } elseif ($categoryName = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        return $query->orderBy('id', 'desc')->paginate(15);
    }

    public function show($id)
    {
        return Product::with('category')->findOrFail($id);
    }
}

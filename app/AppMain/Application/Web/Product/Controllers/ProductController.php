<?php

namespace App\AppMain\Application\Web\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductFlat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ProductFlat::where('status', true);

        if ($request->has('category_id')) {
            // In a real app, you'd join with product_categories
            // but for simplicity with flat table, we might need category_ids in flat
            // or just query the Product model.
            // Let's stick to simple status for now.
        }

        if ($request->has('featured')) {
            $query->where('featured', true);
        }

        if ($request->has('new')) {
            $query->where('new', true);
        }

        $products = $query->paginate($request->get('items_per_page', 12));

        return response()->json($products);
    }

    public function show(string $urlKey): JsonResponse
    {
        $product = ProductFlat::where('url_key', $urlKey)
            ->where('status', true)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}

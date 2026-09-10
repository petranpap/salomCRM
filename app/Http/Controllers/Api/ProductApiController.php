<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::paginate(24);
        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $data['salon_id'] = $user->isSuperAdmin()
            ? $request->input('salon_id')
            : $user->salon_id;

        $product = Product::create($data);
        return response()->json($product, 201);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }

    public function lowStockAlerts(): JsonResponse
    {
        $products = Product::whereColumn('stock_qty', '<=', 'threshold_qty')
            ->where('status', 'active')
            ->get();
        return response()->json($products);
    }
}

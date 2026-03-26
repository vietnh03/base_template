<?php

namespace App\AppMain\Application\Admin\Product\Controllers;

use App\AppMain\Application\Admin\Product\Requests\ProductStoreRequest;
use App\AppMain\Application\Admin\Product\Requests\ProductUpdateRequest;
use App\AppMain\Domain\Product\Services\ProductService;
use App\AppMain\Domain\Product\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductRepository $productRepository
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->productRepository->paginate($request->get('limit', 15));
        return ProductResource::collection($products);
    }

    public function store(ProductStoreRequest $request): ProductResource
    {
        $product = $this->productService->createProduct($request->validated());
        return new ProductResource($product->load(['categories', 'inventories', 'images', 'attribute_values.attribute']));
    }

    public function show(int $id): JsonResponse|ProductResource
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return new ProductResource($product->load(['categories', 'inventories', 'images', 'attribute_values.attribute']));
    }

    public function update(ProductUpdateRequest $request, int $id): JsonResponse|ProductResource
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $this->productService->updateProduct($product, $request->validated());
        return new ProductResource($product->load(['categories', 'inventories', 'images', 'attribute_values.attribute']));
    }

    public function uploadImage(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $image = $product->images()->create([
                'path' => $path,
                'type' => $request->get('type', 'main'),
                'position' => $product->images()->count() + 1,
            ]);
            return response()->json($image, 201);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }

    public function updateInventory(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        $inventory = $product->inventories()->updateOrCreate(
            ['product_id' => $product->id],
            ['qty' => $request->qty]
        );

        return response()->json($inventory);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $this->productRepository->delete($product);
        return response()->json(null, 204);
    }
}
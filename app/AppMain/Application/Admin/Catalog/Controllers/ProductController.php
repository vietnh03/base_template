<?php

namespace App\AppMain\Application\Admin\Catalog\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\DTOs\ProductDTO;
use App\AppMain\Domain\Catalog\Services\ProductService;
use App\AppMain\Application\Admin\Catalog\Requests\ProductRequest;
use App\AppMain\Application\Admin\Catalog\Requests\ProductFilter;
use App\AppMain\Application\Admin\Catalog\Responses\ProductResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        /* $this->authorize('viewAny', \App\Models\Product::class); */
        return $this->baseAction(function () use ($request) {
            $filter = ProductFilter::fromRequest($request);
            $request->validate($filter->validate());
            $products = $this->productService->getProductsWithFilters($filter->toArray());
            return ProductResponse::paginated($products);
        }, 'Products retrieved successfully');
    }

    public function show(string $id)
    {
        $product = $this->productService->findProduct($id);
        /* $this->authorize('view', $product); */

        return $this->baseAction(function () use ($product) {
            return ProductResponse::single($product);
        }, 'Product retrieved successfully');
    }

    public function store(ProductRequest $request)
    {
        /* $this->authorize('create', \App\Models\Product::class); */
        return $this->baseActionTransaction(function () use ($request) {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productService->createProduct($dto);
            return ProductResponse::single($product);
        }, 'Product created successfully');
    }

    public function update(ProductRequest $request, string $id)
    {
        $product = $this->productService->findProduct($id);
        /* $this->authorize('update', $product); */

        return $this->baseActionTransaction(function () use ($request, $id) {
            $dto = ProductDTO::fromRequest($request);
            $this->productService->updateProduct($id, $dto);
            $updatedProduct = $this->productService->findProduct($id);
            return ProductResponse::single($updatedProduct);
        }, 'Product updated successfully');
    }

    public function destroy(string $id)
    {
        $product = $this->productService->findProduct($id);
        /* $this->authorize('delete', $product); */

        return $this->baseActionTransaction(function () use ($id) {
            $this->productService->deleteProduct($id);
            return ['message' => 'Product deleted successfully'];
        }, 'Product deleted successfully');
    }
}

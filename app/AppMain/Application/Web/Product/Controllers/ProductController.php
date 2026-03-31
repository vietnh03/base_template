<?php

namespace App\AppMain\Application\Web\Product\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Catalog\Services\ProductService;
use App\AppMain\Application\Web\Product\Requests\ProductFilter;
use App\AppMain\Application\Web\Product\Responses\ProductResponse;
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
        return $this->baseAction(function () use ($request) {
            $filter = ProductFilter::fromRequest($request);
            $request->validate($filter->validate());
            $products = $this->productService->getWebProductsWithFilters($filter->toArray());
            return ProductResponse::paginated($products);
        }, 'Products retrieved successfully');
    }

    public function show(string $urlKey)
    {
        return $this->baseAction(function () use ($urlKey) {
            $product = $this->productService->findWebProductByUrlKey($urlKey);
            return ProductResponse::single($product);
        }, 'Product retrieved successfully');
    }
}

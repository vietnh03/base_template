<?php

namespace App\AppMain\Domain\Cms\Services;

use App\AppMain\Domain\Cms\Repositories\CmsRepository;
use App\AppMain\Domain\Catalog\Services\ProductService;
use App\AppMain\Application\Api\Cms\Responses\CmsPageResponse;
use App\AppMain\Application\Admin\Catalog\Responses\ProductResponse;

class CmsService
{
    public function __construct(
        protected CmsRepository $cmsRepository,
        protected ProductService $productService
    ) {
    }

    public function getPageData(string $slug, ?string $locale = null): ?CmsPageResponse
    {
        $locale = $locale ?? app()->getLocale();
        $page = $this->cmsRepository->findBySlug($slug, $locale);

        if (!$page) {
            return null;
        }

        $response = CmsPageResponse::fromModel($page);

        // Populate data for sections that require a data source
        $response->sections = array_map(function ($sectionResponse) {
            if ($sectionResponse->requires_data_source) {
                $sectionResponse->data = $this->fetchDataSource($sectionResponse->type, $sectionResponse->content);
            }
            return $sectionResponse;
        }, $response->sections);

        return $response;
    }

    protected function fetchDataSource(string $type, array $content): array
    {
        switch ($type) {
            case 'ProductList':
                $limit = $content['limit'] ?? 10;
                $sort = $content['sort'] ?? 'newest';

                $products = $this->productService->getWebProductsWithFilters([
                    'per_page' => $limit,
                    'status' => 1,
                    'sort' => $sort,
                ]);

                return collect($products->items())->map(function ($product) {
                    return ProductResponse::fromModel($product);
                })->toArray();

            default:
                return [];
        }
    }
}

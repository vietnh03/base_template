<?php

namespace App\AppMain\Domain\Catalog\Services;

use App\AppMain\Domain\Catalog\DTOs\ProductDTO;
use App\AppMain\Domain\Catalog\Repositories\ProductRepository;
use App\AppMain\Domain\Catalog\Repositories\ProductFlatRepository;

class ProductService
{
    public const RELATION_KEYS = \App\Models\Product::RELATION_KEYS;

    protected ProductRepository $productRepository;
    protected ProductFlatRepository $productFlatRepository;

    public function __construct(ProductRepository $productRepository, ProductFlatRepository $productFlatRepository)
    {
        $this->productRepository = $productRepository;
        $this->productFlatRepository = $productFlatRepository;
    }

    public function getProductsWithFilters(array $filters)
    {
        return $this->productRepository->getProductsWithFilters($filters);
    }

    public function getWebProductsWithFilters(array $filters)
    {
        return $this->productFlatRepository->getWebProductsWithFilters($filters);
    }

    public function findWebProductByUrlKey(string $urlKey)
    {
        return $this->productFlatRepository->findByUrlKey($urlKey);
    }

    public function findProduct(string $id, array $with = ['*'])
    {
        return $this->productRepository->findById($id, $with);
    }

    public function createProduct(ProductDTO $dto)
    {
        $data = $dto->toArray();
        $relations = [];

        foreach (self::RELATION_KEYS as $rel) {
            if (array_key_exists($rel, $data)) {
                $relations[$rel] = $data[$rel];
                unset($data[$rel]);
            }
        }

        $product = $this->productRepository->create($data, $relations);

        return $product->load(array_merge(self::RELATION_KEYS, ['flat']));
    }

    public function updateProduct(string $id, ProductDTO $dto): \App\Models\Product
    {
        $data = $dto->onlyFilled();
        $relations = [];

        foreach (self::RELATION_KEYS as $rel) {
            if (array_key_exists($rel, $data)) {
                $relations[$rel] = $data[$rel];
                unset($data[$rel]);
            }
        }

        $product = $this->productRepository->update($id, $data, $relations);

        return $product->load(array_merge(self::RELATION_KEYS, ['flat']));
    }

    public function deleteProduct(string $id): bool
    {
        return $this->productRepository->delete($id);
    }
}

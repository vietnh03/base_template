<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\ProductFlat;

class ProductFlatRepository extends BaseRepository
{
    public function getModel()
    {
        return ProductFlat::class;
    }

    public function getWebProductsWithFilters(array $filters)
    {
        $locale = $filters['locale'] ?? config('app.locale', 'vi');

        $query = $this->model->newQuery();
        $query->where('status', true)
            ->where('locale', $locale);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (!empty($filters['tag_id'])) {
            $tagId = $filters['tag_id'];
            $query->whereIn('product_id', function ($q) use ($tagId) {
                $q->select('product_id')
                    ->from('product_tags')
                    ->where('tag_id', $tagId);
            });
        }

        if (!empty($filters['category_id'])) {
            $categoryId = $filters['category_id'];
            $query->whereIn('product_id', function ($q) use ($categoryId) {
                $q->select('product_id')
                    ->from('product_categories')
                    ->where('category_id', $categoryId);
            });
        }

        if (!empty($filters['featured'])) {
            $query->where('featured', true);
        }

        if (!empty($filters['new'])) {
            $query->where('new', true);
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 12);
    }

    public function findByUrlKey(string $urlKey)
    {
        return $this->model->where('url_key', $urlKey)->where('status', true)->firstOrFail();
    }
}

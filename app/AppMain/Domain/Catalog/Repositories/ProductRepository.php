<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository extends BaseRepository
{
    protected array $defaultRelations = Product::RELATION_KEYS;

    public function getModel()
    {
        return Product::class;
    }

    public function findById($id, array $with = [])
    {
        $query = $this->model->newQuery();

        if (in_array('*', $with)) {
            $with = $this->defaultRelations;
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data, array $relations = [], array $flatData = [])
    {
        $product = $this->model->create($data);

        $this->syncRelations($product, $relations, $flatData, $data);

        return $product;
    }

    public function update($id, array $data, array $relations = [], array $flatData = []): bool
    {
        $product = $this->findById($id);
        if (!$product) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        $product->update($data);

        $this->syncRelations($product, $relations, $flatData, $data);

        return true;
    }

    protected function syncRelations($product, array $relations, array $flatData = [], array $globalData = [])
    {
        if (!empty($flatData)) {
            $this->saveFlatData($product, $flatData, $globalData);
        }

        if (isset($relations['categories'])) {
            $product->categories()->sync($relations['categories']);
        }

        if (isset($relations['tags'])) {
            $product->tags()->sync($relations['tags']);
        }

        if (isset($relations['inventories'])) {
            $this->syncHasMany($product->inventories(), $relations['inventories'], ['qty']);
        }

        if (isset($relations['images'])) {
            $this->syncHasMany($product->images(), $relations['images'], ['path', 'type', 'position']);
        }

        if (isset($relations['attribute_values'])) {
            $allAttributeIds = [];
            foreach ($relations['attribute_values'] as $values) {
                $allAttributeIds = array_merge($allAttributeIds, array_keys($values));
            }
            $attributes = \App\Models\Attribute::whereIn('id', array_unique($allAttributeIds))->get()->keyBy('id');

            foreach ($relations['attribute_values'] as $locale => $values) {
                if ($locale === 'common') {
                    $this->saveAttributeValues($product, $values, null, $attributes);
                } else {
                    $this->saveAttributeValues($product, $values, $locale, $attributes);
                }
            }
        }

        if (isset($relations['up_sells'])) {
            $product->up_sells()->sync($relations['up_sells']);
        }

        if (isset($relations['cross_sells'])) {
            $product->cross_sells()->sync($relations['cross_sells']);
        }

        if (isset($relations['super_attributes'])) {
            $product->super_attributes()->sync($relations['super_attributes']);
        }
    }

    protected function syncHasMany($relation, array $items, array $fillable)
    {
        $existingItems = $relation->get();
        $itemIds = collect($items)->pluck('id')->filter()->toArray();

        // Delete removed items
        $existingItems->each(function ($item) use ($itemIds) {
            if (!in_array($item->id, $itemIds)) {
                $item->delete();
            }
        });

        // Update or Create
        foreach ($items as $itemData) {
            if (isset($itemData['id']) && $item = $existingItems->find($itemData['id'])) {
                $item->update(collect($itemData)->only($fillable)->toArray());
            } else {
                $relation->create(collect($itemData)->only($fillable)->toArray());
            }
        }
    }

    protected function saveAttributeValues($product, array $values, ?string $locale = null, $attributes = null)
    {
        if (!$attributes) {
            $attributes = \App\Models\Attribute::whereIn('id', array_keys($values))->get()->keyBy('id');
        }
        $upsertData = [];

        foreach ($values as $attrId => $value) {
            $attribute = $attributes->get($attrId);
            if (!$attribute)
                continue;

            $column = \App\Models\Attribute::getValueColumn($attribute->type);

            $upsertData[] = [
                'product_id' => $product->id,
                'attribute_id' => $attrId,
                'locale' => $locale,
                $column => $value
            ];
        }

        if (!empty($upsertData)) {
            \App\Models\ProductAttributeValue::upsert(
                $upsertData,
                ['product_id', 'attribute_id', 'locale'],
                ['text_value', 'boolean_value', 'integer_value', 'float_value', 'datetime_value', 'date_value', 'json_value']
            );
        }
    }

    protected function generateUniqueUrlKey($name, $locale, $productId = null): string
    {
        $urlKey = Str::slug($name);
        $originalUrlKey = $urlKey;

        $lock = Cache::lock("product_url_key_{$locale}_{$originalUrlKey}", 5);
        $lock->block(5);

        try {
            $i = 1;
            while (true) {
                $query = DB::table('product_flat')
                    ->where('url_key', $urlKey)
                    ->where('locale', $locale);

                if ($productId) {
                    $query->where('product_id', '!=', $productId);
                }

                if (!$query->exists()) {
                    break;
                }

                $urlKey = $originalUrlKey . '-' . $i++;
            }

            return $urlKey;
        } finally {
            $lock->release();
        }
    }

    protected function saveFlatData($product, array $flatData, array $globalData = [])
    {
        foreach ($flatData as $locale => $flat) {
            $product->flat()->updateOrCreate(
                [
                    'locale' => $locale,
                ],
                array_merge($flat, [
                    'sku' => $product->sku,
                    'attribute_family_id' => $product->attribute_family_id,
                    'status' => $product->status,
                    'weight' => $globalData['weight'] ?? $product->weight ?? null,
                    'new' => $globalData['new'] ?? $product->new ?? false,
                    'featured' => $globalData['featured'] ?? $product->featured ?? false,
                    'visible_individually' => $globalData['visible_individually'] ?? $product->visible_individually ?? true,
                    'url_key' => $this->generateUniqueUrlKey($flat['url_key'] ?? $flat['name'] ?? $product->sku, $locale, $product->id),
                ])
            );
        }
    }

    public function delete($id): bool
    {
        $product = $this->findById($id);
        if (!$product) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }
        return $product->delete();
    }

    public function getProductsWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with(['categories', 'tags', 'flat', 'images', 'inventories']);

        $this->applyLikeFilters($query, collect($filters)->only(['sku'])->toArray());

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $query->whereHas('flat', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }
}

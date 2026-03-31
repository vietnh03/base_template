<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Product;
use App\Models\ProductFlat;
use Illuminate\Support\Facades\DB;

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

    public function create(array $data, array $relations = [])
    {
        return DB::transaction(function () use ($data, $relations) {
            $product = $this->model->create($data);

            $this->syncRelations($product, $relations);

            return $product;
        });
    }

    public function update($id, array $data, array $relations = []): bool
    {
        return DB::transaction(function () use ($id, $data, $relations) {
            $product = $this->findById($id);
            if (!$product) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            $product->update($data);

            $this->syncRelations($product, $relations);

            return true;
        });
    }

    protected function syncRelations($product, array $relations)
    {
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

            // Sync all updated attributes to ProductFlat at once for efficiency
            $this->syncToFlat($product);
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

    /**
     * Sync EAV attribute values to ProductFlat table for all locales.
     * This is called after bulk attribute updates for performance and consistency.
     */
    public function syncToFlat($product)
    {
        $defaultLocale = config('app.locale', 'vi');

        $locales = \App\Models\ProductAttributeValue::where('product_id', $product->id)
            ->pluck('locale')
            ->unique()
            ->filter();

        if ($locales->isEmpty()) {
            $locales = collect([$defaultLocale]);
        }

        // Use the centralized map from ProductFlat model
        $attributeMap = ProductFlat::ATTRIBUTE_MAP;
        $attributes = \App\Models\Attribute::whereIn('code', array_keys($attributeMap))->get()->keyBy('id');

        foreach ($locales as $locale) {
            $attributeValues = \App\Models\ProductAttributeValue::where('product_id', $product->id)
                ->where(function ($q) use ($locale) {
                    $q->where('locale', $locale)
                        ->orWhereNull('locale');
                })
                ->get();

            $flatData = [
                'sku' => $product->sku,
                'status' => $product->status,
                'parent_id' => $product->parent_id,
                'attribute_family_id' => $product->attribute_family_id,
            ];

            foreach ($attributeValues as $value) {
                $attribute = $attributes->get($value->attribute_id);
                if (!$attribute) {
                    continue;
                }
                $column = $attributeMap[$attribute->code];
                $flatData[$column] = $value->{\App\Models\Attribute::getValueColumn($attribute->type)};
            }

            $resolvedLocale = $locale ?? $defaultLocale;

            $flatModel = ProductFlat::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $resolvedLocale],
                $flatData
            );

            // Ensure URL Key uniqueness
            if (empty($flatModel->url_key) || (isset($flatData['url_key']) && $flatData['url_key'] !== $flatModel->url_key)) {
                $urlKeyService = app(\App\AppMain\Domain\Catalog\Services\ProductUrlKeyService::class);
                $name = $flatData['name'] ?? $product->sku;
                $uniqueUrlKey = $urlKeyService->generateUniqueUrlKey($flatData['url_key'] ?? $name, $resolvedLocale, $product->id);
                $flatModel->update(['url_key' => $uniqueUrlKey]);
            }
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

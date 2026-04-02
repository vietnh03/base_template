<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Product;
use App\Models\ProductFlat;
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

    public function create(array $data, array $relations = [])
    {
        return DB::transaction(function () use ($data, $relations) {
            // Extract root-level attributes that should be handled as EAV
            $attributeMap = ProductFlat::ATTRIBUTE_MAP;
            $rootAttributes = [];
            foreach ($attributeMap as $code => $column) {
                if (isset($data[$code])) {
                    $rootAttributes[$code] = $data[$code];
                }
            }

            // Filter out non-Product columns from $data
            $productColumns = ['sku', 'status', 'parent_id', 'attribute_family_id', 'additional', 'cost_price', 'weight', 'thumbnail', 'new', 'featured', 'visible_individually', 'created_at', 'updated_at'];
            $coreData = array_intersect_key($data, array_flip($productColumns));

            // Disable observer during create to prevent partial flat-write before EAV is saved.
            // syncToFlat() is called explicitly after syncRelations().
            $product = Product::withoutEvents(function () use ($coreData) {
                return $this->model->create($coreData);
            });

            // If root attributes exist, merge them into relations['attribute_values']['common']
            if (!empty($rootAttributes)) {
                if (!isset($relations['attribute_values'])) {
                    $relations['attribute_values'] = [];
                }

                // We need to resolve attribute IDs from codes for root attributes
                $attributes = \App\Models\Attribute::whereIn('code', array_keys($rootAttributes))->get()->keyBy('code');
                foreach ($rootAttributes as $code => $value) {
                    $attribute = $attributes->get($code);
                    if ($attribute) {
                        $relations['attribute_values']['common'][$attribute->id] = $value;
                    }
                }
            }

            $relations = $this->handleImages($product, $relations);
            $this->syncRelations($product, $relations);

            // If no attribute_values were provided, still ensure a flat row exists
            if (!isset($relations['attribute_values'])) {
                $this->syncToFlat($product);
            }

            return $product;
        });
    }

    public function update($id, array $data, array $relations = []): Product
    {
        return DB::transaction(function () use ($id, $data, $relations) {
            $product = $this->findById($id);

            // Extract root-level attributes
            $attributeMap = ProductFlat::ATTRIBUTE_MAP;
            $rootAttributes = [];
            foreach ($attributeMap as $code => $column) {
                if (isset($data[$code])) {
                    $rootAttributes[$code] = $data[$code];
                }
            }

            // Filter out non-Product columns from $data
            $productColumns = ['sku', 'status', 'parent_id', 'attribute_family_id', 'additional', 'cost_price', 'weight', 'thumbnail', 'new', 'featured', 'visible_individually', 'updated_at'];
            $coreData = array_intersect_key($data, array_flip($productColumns));

            $product->update($coreData);

            // If root attributes exist, merge them into relations['attribute_values']['common']
            if (!empty($rootAttributes)) {
                if (!isset($relations['attribute_values'])) {
                    $relations['attribute_values'] = [];
                }

                $attributes = \App\Models\Attribute::whereIn('code', array_keys($rootAttributes))->get()->keyBy('code');
                foreach ($rootAttributes as $code => $value) {
                    $attribute = $attributes->get($code);
                    if ($attribute) {
                        $relations['attribute_values']['common'][$attribute->id] = $value;
                    }
                }
            }

            $relations = $this->handleImages($product, $relations);
            $this->syncRelations($product->fresh(), $relations);

            // Nếu không có attribute_values trong relations, vẫn phải sync flat
            // để đảm bảo các scalar field (sku, status, parent_id, v.v.) được cập nhật
            if (!isset($relations['attribute_values'])) {
                $this->syncToFlat($product->fresh());
            }

            return $product->fresh();
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
            $attributeValues = $relations['attribute_values'];

            // Check if it's a flat format (codes/labels) or nested (locales -> IDs)
            // If the first item of the array IS NOT an array, it's likely flat format ['code' => 'value']
            $isFlat = false;
            foreach ($attributeValues as $key => $val) {
                if (!is_array($val)) {
                    $isFlat = true;
                    break;
                }
            }

            if ($isFlat) {
                // Normalize flat format to ['common' => [code => value]]
                $attributeValues = ['common' => $attributeValues];
            }

            $allCodesOrIds = [];
            foreach ($attributeValues as $locale => $values) {
                $allCodesOrIds = array_merge($allCodesOrIds, array_keys($values));
            }

            // Resolve attributes by ID or Code
            $resolvedAttributes = \App\Models\Attribute::whereIn('id', $allCodesOrIds)
                ->orWhereIn('code', $allCodesOrIds)
                ->get();

            $attributeMap = [];
            foreach ($resolvedAttributes as $attr) {
                $attributeMap[$attr->id] = $attr;
                $attributeMap[$attr->code] = $attr;
            }
            $attributes = collect($attributeMap);

            $normalizedAttributeValues = [];

            foreach ($attributeValues as $locale => $values) {
                $localeKey = ($locale === 'common' || empty($locale)) ? null : $locale;
                $normalizedValues = [];

                foreach ($values as $codeOrId => $value) {
                    $attribute = $attributes->get($codeOrId);
                    if (!$attribute)
                        continue;

                    // Resolve option label to ID for select/multiselect if value is string and not numeric
                    if (in_array($attribute->type, ['select', 'multiselect']) && is_string($value) && !is_numeric($value)) {
                        $option = \App\Models\AttributeOption::where('attribute_id', $attribute->id)
                            ->where('admin_name', $value)
                            ->first();
                        if ($option) {
                            $value = $option->id;
                        }
                    }

                    $normalizedValues[$attribute->id] = $value;
                }

                if (!empty($normalizedValues)) {
                    $this->saveAttributeValues($product, $normalizedValues, $localeKey, $attributes);
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
                'id' => (string) Str::uuid(),
                'product_id' => $product->id,
                'attribute_id' => $attribute->id, // Use resolved ID
                'locale' => $locale,
                'text_value' => null,
                'boolean_value' => null,
                'integer_value' => null,
                'float_value' => null,
                'datetime_value' => null,
                'date_value' => null,
                'json_value' => null,
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

        // Load ALL attribute values for this product in ONE query
        $allAttributeValues = \App\Models\ProductAttributeValue::where('product_id', $product->id)->get();

        // Determine locales from current rows; default to app locale if none exist
        $locales = $allAttributeValues->pluck('locale')->unique()->filter();
        if ($locales->isEmpty()) {
            $locales = collect([$defaultLocale]);
        }

        // Use the centralized map from ProductFlat model
        $attributeMap = ProductFlat::ATTRIBUTE_MAP;
        $attributes = \App\Models\Attribute::whereIn('code', array_keys($attributeMap))->get()->keyBy('id');

        // Group by locale in memory (null = common / locale-agnostic values)
        $valuesByLocale = $allAttributeValues->groupBy('locale');

        foreach ($locales as $locale) {
            // Merge locale-specific values on top of common (null locale) values
            $commonValues = $valuesByLocale->get(null, collect());
            $localeValues = $valuesByLocale->get($locale, collect());
            $attributeValues = $commonValues->merge($localeValues);

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

            // Ensure url_key is unique and merge into flatData atomically
            if (isset($flatData['url_key'])) {
                $urlKeyService = app(\App\AppMain\Domain\Catalog\Services\ProductUrlKeyService::class);
                $flatData['url_key'] = $urlKeyService->generateUniqueUrlKey(
                    $flatData['url_key'],
                    $resolvedLocale,
                    $product->id
                );
            } else {
                // Generate url_key from name or sku if the flat row doesn't have one yet
                $existingFlat = ProductFlat::where('product_id', $product->id)
                    ->where('locale', $resolvedLocale)
                    ->first();

                if (!$existingFlat || empty($existingFlat->url_key)) {
                    $urlKeyService = app(\App\AppMain\Domain\Catalog\Services\ProductUrlKeyService::class);
                    $name = $flatData['name'] ?? $product->sku;
                    $flatData['url_key'] = $urlKeyService->generateUniqueUrlKey($name, $resolvedLocale, $product->id);
                }
            }

            ProductFlat::updateOrCreate(
                ['product_id' => $product->id, 'locale' => $resolvedLocale],
                $flatData
            );
        }
    }

    public function delete($id): bool
    {
        $product = $this->findById($id);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($product) {
            // Delete EAV attribute values
            $product->attribute_values()->delete();

            // Delete flat table entries
            $product->flat()->delete();

            // Detach from pivot tables
            $product->categories()->detach();
            $product->tags()->detach();
            $product->up_sells()->detach();
            $product->cross_sells()->detach();
            $product->super_attributes()->detach();

            // Delete associated images and inventories
            $product->images()->delete();
            $product->inventories()->delete();

            // Delete product's children if any (for configurable products)
            foreach ($product->children as $child) {
                $this->delete($child->id);
            }

            return $product->delete();
        });
    }

    public function getProductsWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with(['categories', 'tags', 'flat', 'images', 'inventories']);

        $this->applyLikeFilters($query, collect($filters)->only(['sku'])->toArray());

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['attribute_family_id'])) {
            $query->where('attribute_family_id', $filters['attribute_family_id']);
        }

        if (!empty($filters['name'])) {
            $query->whereHas('flat', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (isset($filters['price_min'])) {
            $query->whereHas('flat', function ($q) use ($filters) {
                $q->where('price', '>=', $filters['price_min']);
            });
        }

        if (isset($filters['price_max'])) {
            $query->whereHas('flat', function ($q) use ($filters) {
                $q->where('price', '<=', $filters['price_max']);
            });
        }

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
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

    protected function handleImages($product, array $relations): array
    {
        if (!isset($relations['images'])) {
            return $relations;
        }

        $images = $relations['images'];
        $processedImages = [];

        // Identify images to keep/update and those to create
        foreach ($images as $imageData) {
            if (isset($imageData['file']) && $imageData['file'] instanceof \Illuminate\Http\UploadedFile) {
                // Upload new image
                $path = $imageData['file']->store('products/' . $product->id, config('filesystems.default'));
                $imageData['path'] = $path;
                unset($imageData['file']);
            }
            $processedImages[] = $imageData;
        }

        // Clean up physically deleted images
        $existingImageIds = array_filter(array_column($processedImages, 'id'));
        $imagesToDelete = $product->images()
            ->when(!empty($existingImageIds), function ($q) use ($existingImageIds) {
                $q->whereNotIn('id', $existingImageIds);
            })
            ->get();

        foreach ($imagesToDelete as $image) {
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($image->path);
        }

        $relations['images'] = $processedImages;

        return $relations;
    }
}

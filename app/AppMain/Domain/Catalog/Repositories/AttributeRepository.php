<?php

namespace App\AppMain\Domain\Catalog\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Attribute;

class AttributeRepository extends BaseRepository
{
    public function getModel()
    {
        return Attribute::class;
    }

    public function findById($id)
    {
        return $this->model->with('options')->findOrFail($id);
    }

    public function create(array $data, array $options = [])
    {
        return \DB::transaction(function () use ($data, $options) {
            $attribute = $this->model->create($data);

            if (!empty($options)) {
                foreach ($options as $optionData) {
                    $attribute->options()->create($optionData);
                }
            }

            return $attribute->load('options');
        });
    }

    public function update($id, array $data, array $options = []): Attribute
    {
        return \DB::transaction(function () use ($id, $data, $options) {
            $attribute = $this->findById($id);

            $attribute->update($data);

            if (!empty($options)) {
                $this->syncHasMany($attribute->options(), $options, ['admin_name', 'swatch_value', 'sort_order']);
            }

            return $attribute->load('options');
        });
    }


    public function getAttributesWithFilters($filters = [])
    {
        $query = $this->model->newQuery();

        $this->applyLikeFilters($query, collect($filters)->only(['code', 'admin_name'])->toArray());

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }

    public function delete($id): bool
    {
        $attribute = $this->findById($id);

        return \DB::transaction(function () use ($attribute) {
            // Delete attribute options
            $attribute->options()->delete();

            // Delete product attribute values associated with this attribute
            \App\Models\ProductAttributeValue::where('attribute_id', $attribute->id)->delete();

            return $attribute->delete();
        });
    }
}

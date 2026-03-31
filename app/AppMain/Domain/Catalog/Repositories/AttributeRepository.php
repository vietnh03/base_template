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

    public function update($id, array $data, array $options = []): bool
    {
        return \DB::transaction(function () use ($id, $data, $options) {
            $attribute = $this->findById($id);
            if (!$attribute) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            $attribute->update($data);

            if (!empty($options)) {
                $this->syncHasMany($attribute->options(), $options, ['admin_name', 'swatch_value', 'sort_order']);
            }

            return true;
        });
    }

    protected function syncHasMany($relation, array $items, array $fillable)
    {
        $existingItems = $relation->get();
        $itemIds = collect($items)->pluck('id')->filter()->toArray();

        // Delete removed items
        $existingItems->each(function ($item) use ($itemIds) {
            if (!empty($item->id) && !in_array($item->id, $itemIds)) {
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
}

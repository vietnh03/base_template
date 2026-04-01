<?php

namespace App\AppMain\Core;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    protected BaseModel $model;
    protected $dbConnection = null;

    public function __construct()
    {
        $this->setModel();
    }

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract public function getModel();

    public function newQuery()
    {
        $newQuery = $this->model->getQuery();
        return $newQuery;
    }

    /**
     * Find record by ID
     */
    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new record
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record by ID
     */
    public function update($id, array $data): mixed
    {
        $record = $this->findById($id);
        return $record->update($data);
    }

    /**
     * Delete record by ID
     */
    public function delete($id): bool
    {
        $record = $this->findById($id);
        return $record->delete();
    }

    /**
     * Apply exact match filters to query
     */
    protected function applyExactFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }
    }

    /**
     * Apply LIKE search filters to query
     */
    protected function applyLikeFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, 'like', "%{$value}%");
            }
        }
    }

    /**
     * Apply date range filters to query
     */
    protected function applyDateFilters(Builder $query, array $dateFilters): void
    {
        foreach ($dateFilters as $config) {
            $field = $config['field'];
            $from = $config['from'] ?? null;
            $to = $config['to'] ?? null;
            $exact = $config['exact'] ?? null;

            if ($exact) {
                $query->whereDate($field, $exact);
            } else {
                if ($from) {
                    $query->whereDate($field, '>=', $from);
                }
                if ($to) {
                    $query->whereDate($field, '<=', $to);
                }
            }
        }
    }

    protected function applyGlobalSearch(Builder $query, array $searchableFields, ?string $searchTerm): void
    {
        if ($searchTerm) {
            $query->where(function (Builder $q) use ($searchableFields, $searchTerm) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            });
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, ?string $sortBy = null, string $sortDirection = 'asc'): void
    {
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDirection);
        }
    }

    protected function applySortingFilter(Builder $query, $filters = []): void
    {
        $sortBy = $filters['sort_by'] ?? null;
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $this->applySorting($query, $sortBy, $sortDirection);
    }

    /**
     * Apply pagination ?per_page&page= to query
     */
    protected function applyPagination(Builder $query, $filters = [])
    {
        if (isset($filters['per_page']) && is_numeric($filters['per_page'])) {
            return $query->paginate((int) $filters['per_page']);
        }
    }

    /**
     * Apply pagination by cursor ?per_page&cursor= to query
     */
    protected function applyCursorPagination(Builder $query, $filters = [])
    {
        if (isset($filters['per_page']) && is_numeric($filters['per_page'])) {
            return $query->cursorPaginate($filters['per_page']);
        }
    }

    /**
     * Sync a HasMany relation: delete removed items, update existing, create new.
     * Common utility shared by ProductRepository and AttributeRepository.
     *
     * @param \Illuminate\Database\Eloquent\Relations\HasMany $relation
     * @param array $items     Array of item data; items with 'id' are updated, others are created
     * @param array $fillable  Columns that may be written
     */
    protected function syncHasMany($relation, array $items, array $fillable): void
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
}
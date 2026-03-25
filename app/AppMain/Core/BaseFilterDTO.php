<?php

namespace App\AppMain\Core;

use Illuminate\Http\Request;

abstract class BaseFilterDTO
{
    public ?string $search = null;
    public ?string $sort_by = null;
    public ?string $sort_direction = 'asc';
    public int $per_page = 15;
    public ?int $limit = null;

    public function __construct(array $data = [])
    {
        $this->search = $data['search'] ?? null;
        $this->sort_by = $data['sort_by'] ?? null;
        $this->sort_direction = $data['sort_direction'] ?? 'asc';
        $this->per_page = isset($data['per_page']) ? (int) $data['per_page'] : 15;
        $this->limit = isset($data['limit']) ? (int) $data['limit'] : null;
        
        // Call child class initialization
        $this->initializeSpecificFields($data);
    }

    /**
     * Initialize specific fields for child classes
     */
    protected function initializeSpecificFields(array $data): void
    {
        // Override in child classes
    }

    /**
     * Create from request - to be implemented by child classes
     */
    public static function fromRequest(Request $request): static
    {
        return new static($request->all());
    }

    /**
     * Get base validation rules
     */
    protected function getBaseValidationRules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|string|in:' . implode(',', $this->getAllowedSortFields()),
            'sort_direction' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:1000',
        ];
    }

    /**
     * Get validation rules including specific rules
     */
    public function validate(): array
    {
        return array_merge(
            $this->getBaseValidationRules(),
            $this->getSpecificValidationRules()
        );
    }

    /**
     * Get specific validation rules for child classes
     */
    protected function getSpecificValidationRules(): array
    {
        return [];
    }

    /**
     * Get allowed sort fields - to be overridden by child classes
     */
    protected function getAllowedSortFields(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        $baseArray = [
            'search' => $this->search,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'per_page' => $this->per_page,
            'limit' => $this->limit,
        ];

        return array_merge($baseArray, $this->getSpecificArray());
    }

    /**
     * Get specific array data for child classes
     */
    protected function getSpecificArray(): array
    {
        return [];
    }

    /**
     * Get only filters (excluding pagination and sorting)
     */
    public function getFiltersOnly(): array
    {
        $filters = $this->toArray();
        unset($filters['sort_by'], $filters['sort_direction'], $filters['per_page'], $filters['limit']);
        
        return array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Check if any filter is applied
     */
    public function hasFilters(): bool
    {
        return !empty($this->getFiltersOnly());
    }

    /**
     * Get pagination settings
     */
    public function getPaginationSettings(): array
    {
        return [
            'per_page' => $this->per_page,
            'limit' => $this->limit,
        ];
    }

    /**
     * Get sorting settings
     */
    public function getSortingSettings(): array
    {
        return [
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
        ];
    }

    /**
     * Check if should use pagination or limit
     */
    public function shouldPaginate(): bool
    {
        return $this->limit === null;
    }
}
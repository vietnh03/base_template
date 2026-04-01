<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class ProductFilter extends BaseFilterDTO
{
    public ?string $sku = null;
    public ?string $name = null;
    public ?int $status = null;
    public ?string $category_id = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->sku = $data['sku'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
        $this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'sku' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
            'category_id' => 'nullable|string|exists:categories,id',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['sku', 'created_at', 'updated_at', 'cost_price'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'status' => $this->status,
            'category_id' => $this->category_id,
        ];
    }
}

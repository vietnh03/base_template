<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class ProductFilter extends BaseFilterDTO
{
    public ?string $sku = null;
    public ?string $name = null;
    public ?int $status = null;
    public ?string $category_id = null;
    public ?float $price_min = null;
    public ?float $price_max = null;
    public ?string $attribute_family_id = null;
    public ?string $tag_id = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->sku = $data['sku'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
        $this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
        $this->price_min = isset($data['price_min']) ? (float) $data['price_min'] : null;
        $this->price_max = isset($data['price_max']) ? (float) $data['price_max'] : null;
        $this->attribute_family_id = $data['attribute_family_id'] ?? null;
        $this->tag_id = $data['tag_id'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'sku' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
            'category_id' => 'nullable|string|exists:categories,id',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'attribute_family_id' => 'nullable|string|exists:attribute_families,id',
            'tag_id' => 'nullable|string|exists:tags,id',
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
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'attribute_family_id' => $this->attribute_family_id,
            'tag_id' => $this->tag_id,
        ];
    }
}

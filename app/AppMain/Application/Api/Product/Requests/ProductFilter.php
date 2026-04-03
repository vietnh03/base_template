<?php

namespace App\AppMain\Application\Api\Product\Requests;

use App\AppMain\Core\BaseFilterDTO;

class ProductFilter extends BaseFilterDTO
{
    public ?string $category_id = null;
    public ?bool $featured = null;
    public ?bool $new = null;
    public ?float $price_min = null;
    public ?float $price_max = null;
    public ?string $name = null;
    public ?string $tag_id = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
        $this->featured = isset($data['featured']) ? (bool) $data['featured'] : null;
        $this->new = isset($data['new']) ? (bool) $data['new'] : null;
        $this->price_min = isset($data['price_min']) ? (float) $data['price_min'] : null;
        $this->price_max = isset($data['price_max']) ? (float) $data['price_max'] : null;
        $this->name = $data['name'] ?? null;
        $this->tag_id = $data['tag_id'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'category_id' => 'nullable|string|exists:categories,id',
            'featured' => 'nullable|boolean',
            'new' => 'nullable|boolean',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'tag_id' => 'nullable|string|exists:tags,id',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['name', 'price', 'created_at', 'updated_at', 'new', 'featured'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'featured' => $this->featured,
            'new' => $this->new,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'name' => $this->name,
            'tag_id' => $this->tag_id,
        ];
    }
}

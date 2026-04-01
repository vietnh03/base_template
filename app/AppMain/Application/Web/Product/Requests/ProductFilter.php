<?php

namespace App\AppMain\Application\Web\Product\Requests;

use App\AppMain\Core\BaseFilterDTO;

class ProductFilter extends BaseFilterDTO
{
    public ?string $category_id = null;
    public ?bool $featured = null;
    public ?bool $new = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
        $this->featured = isset($data['featured']) ? (bool) $data['featured'] : null;
        $this->new = isset($data['new']) ? (bool) $data['new'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'category_id' => 'nullable|string|exists:categories,id',
            'featured' => 'nullable|boolean',
            'new' => 'nullable|boolean',
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
        ];
    }
}

<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class TagFilter extends BaseFilterDTO
{
    public ?string $name = null;
    public ?string $slug = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->name = $data['name'] ?? null;
        $this->slug = $data['slug'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['name', 'slug', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}

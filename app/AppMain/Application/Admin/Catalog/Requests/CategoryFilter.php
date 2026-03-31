<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class CategoryFilter extends BaseFilterDTO
{
    public ?string $name = null;
    public ?int $status = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->name = $data['name'] ?? null;
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['position', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}

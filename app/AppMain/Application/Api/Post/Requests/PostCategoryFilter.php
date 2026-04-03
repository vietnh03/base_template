<?php

namespace App\AppMain\Application\Api\Post\Requests;

use App\AppMain\Core\BaseFilterDTO;

class PostCategoryFilter extends BaseFilterDTO
{
    public ?string $parent_id = null;
    public ?bool $status = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->parent_id = $data['parent_id'] ?? null;
        $this->status = isset($data['status']) ? (bool) $data['status'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'parent_id' => 'nullable|uuid',
            'status' => 'nullable|boolean',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'created_at', 'updated_at', 'position', 'status'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'parent_id' => $this->parent_id,
            'status' => $this->status,
        ];
    }
}

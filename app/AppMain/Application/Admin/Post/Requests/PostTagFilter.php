<?php

namespace App\AppMain\Application\Admin\Post\Requests;

use App\AppMain\Core\BaseFilterDTO;

class PostTagFilter extends BaseFilterDTO
{
    public ?bool $status = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->status = isset($data['status']) ? (bool) $data['status'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'status' => 'nullable|boolean',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'created_at', 'updated_at', 'status'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}

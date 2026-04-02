<?php

namespace App\AppMain\Application\Admin\Post\Requests;

use App\AppMain\Core\BaseFilterDTO;

class PostFilter extends BaseFilterDTO
{
    public ?string $category_id = null;
    public ?string $tag_id = null;
    public ?bool $status = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->category_id = $data['category_id'] ?? null;
        $this->tag_id = $data['tag_id'] ?? null;
        $this->status = isset($data['status']) ? (bool) $data['status'] : null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'category_id' => 'nullable|uuid',
            'tag_id' => 'nullable|uuid',
            'status' => 'nullable|boolean',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'created_at', 'updated_at', 'published_at', 'status'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'tag_id' => $this->tag_id,
            'status' => $this->status,
        ];
    }
}

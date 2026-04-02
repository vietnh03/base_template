<?php

namespace App\AppMain\Application\Admin\User\Requests;

use App\AppMain\Core\BaseFilterDTO;

class UserFilter extends BaseFilterDTO
{
    public ?int $status = null;
    public ?string $gender = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
        $this->gender = $data['gender'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'status' => 'nullable|integer',
            'gender' => 'nullable|string',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['name', 'email', 'status', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'status' => $this->status,
            'gender' => $this->gender,
        ];
    }
}

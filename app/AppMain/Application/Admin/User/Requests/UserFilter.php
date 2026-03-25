<?php

namespace App\AppMain\Application\Admin\User\Requests;

use App\AppMain\Core\BaseFilterDTO;

class UserFilter extends BaseFilterDTO
{
    // User-specific filter properties
    public ?string $name = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $status = null;
    public ?string $created_from = null;
    public ?string $created_to = null;

    /**
     * Initialize user-specific fields
     */
    protected function initializeSpecificFields(array $data): void
    {
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->role = $data['role'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->created_from = $data['created_from'] ?? null;
        $this->created_to = $data['created_to'] ?? null;
    }

    /**
     * Get user-specific validation rules
     */
    protected function getSpecificValidationRules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'role' => 'nullable|in:admin,manager,user',
            'status' => 'nullable|in:active,inactive,suspended',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date|after_or_equal:created_from',
        ];
    }

    /**
     * Get allowed sort fields for users
     */
    protected function getAllowedSortFields(): array
    {
        return [
            'name',
            'email',
            'role',
            'status',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get user-specific array data
     */
    protected function getSpecificArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'created_from' => $this->created_from,
            'created_to' => $this->created_to,
        ];
    }
}

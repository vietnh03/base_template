<?php

namespace App\AppMain\Application\User\Customer\Requests;

use App\AppMain\Core\BaseFilterDTO;

class CustomerFilter extends BaseFilterDTO
{
    // Customer-specific filter properties
    public ?string $id = null;
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?int $status = null;
    public ?bool $is_verified = null;
    public ?string $date_of_birth = null;
    public ?string $date_of_birth_from = null;
    public ?string $date_of_birth_to = null;
    public ?string $gender = null;
    public ?string $notes = null;
    public ?int $age_from = null;
    public ?int $age_to = null;

    /**
     * Initialize customer-specific fields
     */
    protected function initializeSpecificFields(array $data): void
    {
        $this->name = $data['name'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
        $this->is_verified = isset($data['is_verified']) ? (bool) $data['is_verified'] : null;
        $this->date_of_birth = $data['date_of_birth'] ?? null;
        $this->date_of_birth_from = $data['date_of_birth_from'] ?? null;
        $this->date_of_birth_to = $data['date_of_birth_to'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->notes = $data['notes'] ?? null;
        $this->age_from = isset($data['age_from']) ? (int) $data['age_from'] : null;
        $this->age_to = isset($data['age_to']) ? (int) $data['age_to'] : null;
    }

    /**
     * Get customer-specific validation rules
     */
    protected function getSpecificValidationRules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|integer',
            'is_verified' => 'nullable|boolean',
            'date_of_birth' => 'nullable|date',
            'date_of_birth_from' => 'nullable|date',
            'date_of_birth_to' => 'nullable|date|after_or_equal:date_of_birth_from',
            'gender' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'age_from' => 'nullable|integer|min:0|max:120',
            'age_to' => 'nullable|integer|min:0|max:120|gte:age_from',
        ];
    }

    /**
     * Get allowed sort fields for customers
     */
    protected function getAllowedSortFields(): array
    {
        return [
            'name',
            'phone',
            'email',
            'status',
            'is_verified',
            'date_of_birth',
            'gender',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get customer-specific array data
     */
    protected function getSpecificArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'is_verified' => $this->is_verified,
            'date_of_birth' => $this->date_of_birth,
            'date_of_birth_from' => $this->date_of_birth_from,
            'date_of_birth_to' => $this->date_of_birth_to,
            'gender' => $this->gender,
            'notes' => $this->notes,
            'age_from' => $this->age_from,
            'age_to' => $this->age_to,
        ];
    }

    /**
     * Factory methods for common filters
     */
    public static function onlyActive(): self
    {
        return new self(['status' => 1]);
    }

    public static function onlyVerified(): self
    {
        return new self(['is_verified' => true]);
    }

    public static function ageRange(int $from, int $to): self
    {
        return new self([
            'age_from' => $from,
            'age_to' => $to
        ]);
    }

    public static function birthdateRange(string $from, string $to): self
    {
        return new self([
            'date_of_birth_from' => $from,
            'date_of_birth_to' => $to
        ]);
    }
}

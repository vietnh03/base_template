<?php

namespace App\AppMain\Application\User\Customer\Requests;

use App\AppMain\Core\BaseFilterDTO;

class CustomerFilter extends BaseFilterDTO
{
    // Customer-specific filter properties
    public ?string $id = null;
    public ?string $full_name = null;
    public ?string $phone_number = null;
    public ?string $email = null;
    public ?string $customer_type = null;
    public ?string $customer_status = null;
    public ?string $assigned_staff_id = null;
    public ?string $address = null;
    public ?string $date_of_birth = null;
    public ?string $date_of_birth_from = null;
    public ?string $date_of_birth_to = null;
    public ?string $gender = null;
    public ?string $source = null;
    public ?string $notes = null;
    public ?int $age_from = null;
    public ?int $age_to = null;

    /**
     * Initialize customer-specific fields
     */
    protected function initializeSpecificFields(array $data): void
    {
        $this->full_name = $data['full_name'] ?? null;
        $this->phone_number = $data['phone_number'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->customer_type = $data['customer_type'] ?? null;
        $this->customer_status = $data['customer_status'] ?? null;
        $this->assigned_staff_id = $data['assigned_staff_id'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->date_of_birth = $data['date_of_birth'] ?? null;
        $this->date_of_birth_from = $data['date_of_birth_from'] ?? null;
        $this->date_of_birth_to = $data['date_of_birth_to'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->source = $data['source'] ?? null;
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
            'full_name' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'customer_type' => 'nullable|in:Individual,Business',
            'customer_status' => 'nullable|in:Lead,Active,Inactive,VIP',
            'assigned_staff_id' => 'nullable|uuid',
            'address' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date',
            'date_of_birth_from' => 'nullable|date',
            'date_of_birth_to' => 'nullable|date|after_or_equal:date_of_birth_from',
            'gender' => 'nullable|in:Male,Female,Other',
            'source' => 'nullable|string|max:50',
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
            'full_name',
            'phone_number',
            'email',
            'customer_type',
            'customer_status',
            'assigned_staff_id',
            'date_of_birth',
            'gender',
            'source',
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
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'customer_type' => $this->customer_type,
            'customer_status' => $this->customer_status,
            'assigned_staff_id' => $this->assigned_staff_id,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'date_of_birth_from' => $this->date_of_birth_from,
            'date_of_birth_to' => $this->date_of_birth_to,
            'gender' => $this->gender,
            'source' => $this->source,
            'notes' => $this->notes,
            'age_from' => $this->age_from,
            'age_to' => $this->age_to,
        ];
    }

    /**
     * Factory methods for common filters
     */
    public static function onlyVip(): self
    {
        return new self(['customer_status' => 'VIP']);
    }

    public static function onlyActive(): self
    {
        return new self(['customer_status' => 'Active']);
    }

    public static function onlyLeads(): self
    {
        return new self(['customer_status' => 'Lead']);
    }

    public static function onlyInactive(): self
    {
        return new self(['customer_status' => 'Inactive']);
    }

    public static function bySource(string $source): self
    {
        return new self(['source' => $source]);
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

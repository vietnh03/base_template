<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use App\AppMain\Core\BaseFilterDTO;

class OrderFilter extends BaseFilterDTO
{
    public ?string $status = null;
    public ?string $customer_email = null;
    public ?string $increment_id = null;
    public ?string $created_after = null;
    public ?string $created_before = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->status = $data['status'] ?? null;
        $this->customer_email = $data['customer_email'] ?? null;
        $this->increment_id = $data['increment_id'] ?? null;
        $this->created_after = $data['created_after'] ?? null;
        $this->created_before = $data['created_before'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'status' => 'nullable|string',
            'customer_email' => 'nullable|string',
            'increment_id' => 'nullable|string',
            'created_after' => 'nullable|date',
            'created_before' => 'nullable|date',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'increment_id', 'status', 'grand_total', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'status' => $this->status,
            'customer_email' => $this->customer_email,
            'increment_id' => $this->increment_id,
            'created_after' => $this->created_after,
            'created_before' => $this->created_before,
        ];
    }
}

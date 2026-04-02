<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use App\AppMain\Core\BaseFilterDTO;

class OrderTransactionFilter extends BaseFilterDTO
{
    public ?string $order_id = null;
    public ?string $payment_method = null;
    public ?string $status = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->order_id = $data['order_id'] ?? null;
        $this->payment_method = $data['payment_method'] ?? null;
        $this->status = $data['status'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'order_id' => 'nullable|uuid',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|string',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'order_id', 'payment_method', 'status', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
        ];
    }
}

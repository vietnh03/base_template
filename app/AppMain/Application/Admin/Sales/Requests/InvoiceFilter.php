<?php

namespace App\AppMain\Application\Admin\Sales\Requests;

use App\AppMain\Core\BaseFilterDTO;

class InvoiceFilter extends BaseFilterDTO
{
    public ?string $order_id = null;
    public ?string $state = null;

    protected function initializeSpecificFields(array $data): void
    {
        $this->order_id = $data['order_id'] ?? null;
        $this->state = $data['state'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'order_id' => 'nullable|uuid',
            'state' => 'nullable|string',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'order_id', 'state', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'state' => $this->state,
        ];
    }
}

<?php

namespace App\AppMain\Domain\Sales\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function getModel()
    {
        return Order::class;
    }

    public function getOrdersWithFilters($filters = [])
    {
        $query = $this->model->newQuery()->with(['items', 'addresses', 'customer']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_email'])) {
            $query->where('customer_email', 'like', '%' . $filters['customer_email'] . '%');
        }

        if (!empty($filters['increment_id'])) {
            $query->where('increment_id', 'like', '%' . $filters['increment_id'] . '%');
        }

        if (!empty($filters['created_after'])) {
            $query->whereDate('created_at', '>=', $filters['created_after']);
        }

        if (!empty($filters['created_before'])) {
            $query->whereDate('created_at', '<=', $filters['created_before']);
        }

        $this->applySortingFilter($query, $filters);

        return $this->applyPagination($query, $filters) ?? $query->paginate($filters['per_page'] ?? 15);
    }
}

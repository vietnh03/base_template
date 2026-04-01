<?php

namespace App\AppMain\Domain\Sales\Services;

use App\AppMain\Domain\Sales\Repositories\OrderTransactionRepository;
use App\AppMain\Domain\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderTransactionService
{
    protected OrderTransactionRepository $transactionRepository;
    protected OrderRepository $orderRepository;

    public function __construct(
        OrderTransactionRepository $transactionRepository,
        OrderRepository $orderRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->transactionRepository->getModel()::query();
        return $query->paginate($filters['limit'] ?? 10);
    }

    public function findById($id, array $with = ['order', 'invoice'])
    {
        return $this->transactionRepository->getModel()::with($with)->findOrFail($id);
    }

    public function create(array $data)
    {
        // Simple create wrapper
        return $this->transactionRepository->create($data);
    }
}

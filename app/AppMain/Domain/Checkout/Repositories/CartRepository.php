<?php

namespace App\AppMain\Domain\Checkout\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Cart;

class CartRepository extends BaseRepository
{
    public function getModel()
    {
        return Cart::class;
    }

    public function findActiveByCustomerId(string $customerId): ?Cart
    {
        return $this->model->where('customer_id', $customerId)
            ->where('is_active', true)
            ->first();
    }

    public function findActiveByGuestId(string $cartId): ?Cart
    {
        return $this->model->where('id', $cartId)
            ->where('is_active', true)
            ->first();
    }
}

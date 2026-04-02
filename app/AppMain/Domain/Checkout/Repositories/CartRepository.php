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

    public function findActiveByUserId(string $userId): ?Cart
    {
        return $this->model->where('user_id', $userId)
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

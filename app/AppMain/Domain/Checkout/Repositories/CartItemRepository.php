<?php

namespace App\AppMain\Domain\Checkout\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\CartItem;

class CartItemRepository extends BaseRepository
{
    public function getModel()
    {
        return CartItem::class;
    }
}

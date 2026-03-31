<?php

namespace App\AppMain\Domain\Sales\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
    public function getModel()
    {
        return OrderItem::class;
    }
}

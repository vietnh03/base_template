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
}

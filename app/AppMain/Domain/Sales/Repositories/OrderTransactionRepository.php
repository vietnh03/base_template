<?php

namespace App\AppMain\Domain\Sales\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\OrderTransaction;

class OrderTransactionRepository extends BaseRepository
{
    public function getModel()
    {
        return OrderTransaction::class;
    }
}

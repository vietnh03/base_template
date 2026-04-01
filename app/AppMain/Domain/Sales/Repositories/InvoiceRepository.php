<?php

namespace App\AppMain\Domain\Sales\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Invoice;

class InvoiceRepository extends BaseRepository
{
    public function getModel()
    {
        return Invoice::class;
    }
}

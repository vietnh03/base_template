<?php

namespace App\AppMain\Domain\Sales\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\InvoiceItem;

class InvoiceItemRepository extends BaseRepository
{
    public function getModel()
    {
        return InvoiceItem::class;
    }
}

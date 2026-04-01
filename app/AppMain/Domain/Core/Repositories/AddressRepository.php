<?php

namespace App\AppMain\Domain\Core\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Address;

class AddressRepository extends BaseRepository
{
    public function getModel()
    {
        return Address::class;
    }
}

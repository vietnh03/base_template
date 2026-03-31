<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class TagDTO extends BaseDTO
{
    public $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->translations = $data['translations'] ?? [];
    }
}

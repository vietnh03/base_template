<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class TagDTO extends BaseDTO
{
    public $status;
    public $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->status = $data['status'] ?? 0;
        $this->translations = $data['translations'] ?? [];
    }
}

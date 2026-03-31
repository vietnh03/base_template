<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class CategoryDTO extends BaseDTO
{
    public $parent_id;
    public $position;
    public $status = true;
    public $additional;
    public $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->translations = $data['translations'] ?? [];
    }
}

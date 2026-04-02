<?php

namespace App\AppMain\Domain\Post\DTOs;

use App\AppMain\Core\BaseDTO;

class PostTagDTO extends BaseDTO
{
    public bool $status = true;
    public array $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->translations = $data['translations'] ?? [];
    }
}

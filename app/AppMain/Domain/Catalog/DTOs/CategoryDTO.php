<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class CategoryDTO extends BaseDTO
{
    public ?string $parent_id = null;
    public ?int $position = null;
    public bool $status = true;
    public ?array $additional = null;
    public array $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->translations = $data['translations'] ?? [];
    }
}

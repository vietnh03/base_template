<?php

namespace App\AppMain\Domain\Post\DTOs;

use App\AppMain\Core\BaseDTO;

class PostCategoryDTO extends BaseDTO
{
    public ?string $parent_id = null;
    public bool $status = true;
    public int $position = 0;
    public $image = null;
    public array $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->image = $data['image'] ?? null;
        $this->translations = $data['translations'] ?? [];
    }
}

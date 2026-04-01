<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class CategoryDTO extends BaseDTO
{
    public ?string $parent_id = null;
    public ?int $position = null;
    public bool $status = true;
    public ?array $additional = null;
    public $logo = null;
    public $banner = null;
    public array $translations = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->logo = $data['logo'] ?? null;
        $this->banner = $data['banner'] ?? null;
        $this->translations = $data['translations'] ?? [];
    }
}

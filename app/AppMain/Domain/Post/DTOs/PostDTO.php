<?php

namespace App\AppMain\Domain\Post\DTOs;

use App\AppMain\Core\BaseDTO;

class PostDTO extends BaseDTO
{
    public bool $status = true;
    public ?string $author_id = null;
    public ?string $published_at = null;
    public $image = null;
    public array $translations = [];
    public array $categories = [];
    public array $tags = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->image = $data['image'] ?? null;
        $this->translations = $data['translations'] ?? [];
        $this->categories = $data['categories'] ?? [];
        $this->tags = $data['tags'] ?? [];
    }
}

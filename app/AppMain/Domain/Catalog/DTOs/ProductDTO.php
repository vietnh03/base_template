<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class ProductDTO extends BaseDTO
{
    public string $sku;
    public bool $status = true;
    public ?int $parent_id = null;
    public int $attribute_family_id = 1;
    public ?array $additional = null;

    public ?float $weight = null;
    public ?string $thumbnail = null;
    public bool $new = false;
    public bool $featured = false;

    // Relationships
    public array $categories = [];
    public array $tags = [];
    public array $inventories = [];
    public array $images = [];
    public array $attribute_values = [];
    public array $up_sells = [];
    public array $cross_sells = [];
    public array $super_attributes = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->categories = $data['categories'] ?? [];
        $this->tags = $data['tags'] ?? [];
        $this->inventories = $data['inventories'] ?? [];
        $this->images = $data['images'] ?? [];
        $this->attribute_values = $data['attribute_values'] ?? [];
        $this->up_sells = $data['up_sells'] ?? [];
        $this->cross_sells = $data['cross_sells'] ?? [];
        $this->super_attributes = $data['super_attributes'] ?? [];
    }
}

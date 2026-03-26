<?php

namespace App\AppMain\Domain\Product\DTOs;

class ProductDTO
{
    public function __construct(
        public string $sku,
        public string $type,
        public bool $status,
        public array $categories = [],
        public array $attributes = [],
        public ?int $parent_id = null,
        public ?int $attribute_family_id = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            sku: $data['sku'],
            type: $data['type'] ?? 'simple',
            status: $data['status'] ?? true,
            categories: $data['categories'] ?? [],
            attributes: $data['attributes'] ?? [],
            parent_id: $data['parent_id'] ?? null,
            attribute_family_id: $data['attribute_family_id'] ?? null,
        );
    }
}

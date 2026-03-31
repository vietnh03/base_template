<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class AttributeDTO extends BaseDTO
{
    public string $code;
    public string $admin_name;
    public string $type;
    public bool $is_required = false;
    public bool $is_unique = false;
    public bool $is_filterable = false;
    public bool $is_configurable = false;
    public array $options = [];

    public function __construct(array $data)
    {
        // Cast boolean fields before delegating to parent
        foreach (['is_required', 'is_unique', 'is_filterable', 'is_configurable'] as $boolField) {
            if (array_key_exists($boolField, $data)) {
                $data[$boolField] = (bool) $data[$boolField];
            }
        }

        parent::__construct($data);

        $this->options = $data['options'] ?? [];
    }
}

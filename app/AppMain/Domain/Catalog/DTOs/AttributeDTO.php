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
        $this->code = $data['code'];
        $this->admin_name = $data['admin_name'];
        $this->type = $data['type'];
        $this->is_required = (bool) ($data['is_required'] ?? false);
        $this->is_unique = (bool) ($data['is_unique'] ?? false);
        $this->is_filterable = (bool) ($data['is_filterable'] ?? false);
        $this->is_configurable = (bool) ($data['is_configurable'] ?? false);
        $this->options = $data['options'] ?? [];
    }
}

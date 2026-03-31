<?php

namespace App\AppMain\Domain\Catalog\DTOs;

use App\AppMain\Core\BaseDTO;

class AttributeFamilyDTO extends BaseDTO
{
    public string $code;
    public string $name;
    public bool $status = true;
    public array $groups = [];

    public function __construct(array $data)
    {
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->status = (bool) ($data['status'] ?? true);
        $this->groups = $data['groups'] ?? [];
    }
}

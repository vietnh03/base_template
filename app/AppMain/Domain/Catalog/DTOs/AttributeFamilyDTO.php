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
        $data['status'] = (bool) ($data['status'] ?? true);
        parent::__construct($data);
        $this->groups = $data['groups'] ?? [];
    }
}

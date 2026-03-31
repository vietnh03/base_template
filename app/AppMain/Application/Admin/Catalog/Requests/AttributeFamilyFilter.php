<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class AttributeFamilyFilter extends BaseFilterDTO
{
    public ?string $code = null;
    public ?string $name = null;

    public function validate(): array
    {
        return [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ];
    }
}

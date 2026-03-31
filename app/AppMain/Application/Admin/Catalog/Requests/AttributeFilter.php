<?php

namespace App\AppMain\Application\Admin\Catalog\Requests;

use App\AppMain\Core\BaseFilterDTO;

class AttributeFilter extends BaseFilterDTO
{
    public ?string $code = null;
    public ?string $admin_name = null;
    public ?string $type = null;

    public function validate(): array
    {
        return [
            'code' => 'nullable|string',
            'admin_name' => 'nullable|string',
            'type' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ];
    }
}

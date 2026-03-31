<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class AttributeResponse extends BaseResponseDTO
{
    public string $id;
    public string $code;
    public string $adminName;
    public string $type;
    public bool $isRequired;
    public bool $isUnique;
    public bool $isFilterable;
    public bool $isConfigurable;
    public array $options = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->code = $model->code;
        $dto->adminName = $model->admin_name;
        $dto->type = $model->type;
        $dto->isRequired = (bool) $model->is_required;
        $dto->isUnique = (bool) $model->is_unique;
        $dto->isFilterable = (bool) $model->is_filterable;
        $dto->isConfigurable = (bool) $model->is_configurable;

        if ($model->relationLoaded('options')) {
            $dto->options = $model->options->map(fn($o) => [
                'id' => $o->id,
                'admin_name' => $o->admin_name,
                'swatch_value' => $o->swatch_value,
                'sort_order' => $o->sort_order,
            ])->toArray();
        }

        return $dto;
    }
}

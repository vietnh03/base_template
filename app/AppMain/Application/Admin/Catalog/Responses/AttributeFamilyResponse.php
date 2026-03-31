<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class AttributeFamilyResponse extends BaseResponseDTO
{
    public string $id;
    public string $code;
    public string $name;
    public bool $status;
    public array $groups = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->code = $model->code;
        $dto->name = $model->name;
        $dto->status = (bool) $model->status;

        if ($model->relationLoaded('groups')) {
            $dto->groups = $model->groups->map(fn($g) => [
                'id' => $g->id,
                'name' => $g->name,
                'position' => $g->position,
                'attributes' => $g->attribute_group_mappings->map(fn($m) => [
                    'id' => $m->attribute->id ?? null,
                    'code' => $m->attribute->code ?? '',
                    'admin_name' => $m->attribute->admin_name ?? '',
                ])->toArray(),
            ])->toArray();
        }

        return $dto;
    }
}

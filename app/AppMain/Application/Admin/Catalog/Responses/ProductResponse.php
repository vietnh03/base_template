<?php

namespace App\AppMain\Application\Admin\Catalog\Responses;

use App\AppMain\Core\BaseResponseDTO;

class ProductResponse extends BaseResponseDTO
{
    public string $id;
    public string $sku;
    public bool $status;
    public ?float $costPrice;
    public ?float $weight;
    public ?string $thumbnail;
    public bool $new;
    public bool $featured;
    public array $flat = [];
    public array $categories = [];
    public array $tags = [];
    public array $images = [];
    public array $inventories = [];
    public array $attributeValues = [];
    public array $upSells = [];
    public array $crossSells = [];
    public array $superAttributes = [];
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->sku = $model->sku;
        $dto->status = (bool) $model->status;
        $dto->costPrice = $model->cost_price ? (float) $model->cost_price : null;
        $dto->weight = $model->weight ? (float) $model->weight : null;
        $dto->thumbnail = $model->thumbnail;
        $dto->new = (bool) $model->new;
        $dto->featured = (bool) $model->featured;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        if ($model->relationLoaded('flat')) {
            foreach ($model->flat as $flat) {
                $dto->flat[$flat->locale] = [
                    'name' => $flat->name,
                    'description' => $flat->description,
                    'price' => (float) $flat->price,
                    'urlKey' => $flat->url_key,
                    'metaTitle' => $flat->meta_title,
                    'metaKeywords' => $flat->meta_keywords,
                    'metaDescription' => $flat->meta_description,
                ];
            }
        }

        if ($model->relationLoaded('categories')) {
            $dto->categories = $model->categories->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name ?? '', // Improved
            ])->toArray();
        }

        if ($model->relationLoaded('tags')) {
            $dto->tags = $model->tags->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
            ])->toArray();
        }

        if ($model->relationLoaded('images')) {
            $dto->images = $model->images->map(fn($i) => [
                'id' => $i->id,
                'path' => $i->path,
                'type' => $i->type,
                'position' => $i->position,
            ])->toArray();
        }

        if ($model->relationLoaded('inventories')) {
            $dto->inventories = $model->inventories->map(fn($inv) => [
                'id' => $inv->id,
                'qty' => $inv->qty,
            ])->toArray();
        }

        if ($model->relationLoaded('attribute_values')) {
            $dto->attributeValues = $model->attribute_values->map(fn($av) => [
                'attribute_id' => $av->attribute_id,
                'locale' => $av->locale,
                'value' => $av->text_value ?? $av->boolean_value ?? $av->integer_value ?? $av->float_value ?? $av->datetime_value ?? $av->date_value ?? $av->json_value,
            ])->toArray();
        }

        if ($model->relationLoaded('up_sells')) {
            $dto->upSells = $model->up_sells->map(fn($p) => ['id' => $p->id, 'sku' => $p->sku])->toArray();
        }

        if ($model->relationLoaded('cross_sells')) {
            $dto->crossSells = $model->cross_sells->map(fn($p) => ['id' => $p->id, 'sku' => $p->sku])->toArray();
        }

        if ($model->relationLoaded('super_attributes')) {
            $dto->superAttributes = $model->super_attributes->map(fn($a) => ['id' => $a->id, 'code' => $a->code])->toArray();
        }

        return $dto;
    }
}

<?php

namespace App\AppMain\Application\Api\Catalog\Product\Responses;

use App\AppMain\Core\BaseResponseDTO;

class ProductResponse extends BaseResponseDTO
{
    public string $id; // product_id
    public string $sku;
    public string $name;
    public ?string $shortDescription;
    public ?string $description;
    public string $urlKey;
    public bool $new;
    public bool $featured;
    public ?string $thumbnail;
    public float $price;
    public ?float $specialPrice;
    public bool $status;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->product_id;
        $dto->sku = $model->sku ?? '';
        $dto->name = $model->name ?? '';
        $dto->shortDescription = $model->short_description;
        $dto->description = $model->description;
        $dto->urlKey = $model->url_key ?? '';
        $dto->new = (bool) $model->new;
        $dto->featured = (bool) $model->featured;
        $dto->thumbnail = $model->thumbnail;
        $dto->price = (float) $model->price;
        $dto->specialPrice = $model->special_price ? (float) $model->special_price : null;
        $dto->status = (bool) $model->status;
        return $dto;
    }
}

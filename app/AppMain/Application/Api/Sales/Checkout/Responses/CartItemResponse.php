<?php

namespace App\AppMain\Application\Api\Sales\Checkout\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CartItemResponse extends BaseResponseDTO
{
    public string $id;
    public string $sku;
    public string $name;
    public int $quantity;
    public float $price;
    public float $total;
    public ?string $product_id;

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->sku = $model->sku;
        $dto->name = $model->name;
        $dto->quantity = (int) $model->quantity;
        $dto->price = (float) $model->price;
        $dto->total = (float) $model->total;
        $dto->product_id = (string) $model->product_id;
        return $dto;
    }
}

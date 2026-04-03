<?php

namespace App\AppMain\Application\Api\Sales\Checkout\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CartResponse extends BaseResponseDTO
{
    public string $id;
    public ?string $user_email;
    public ?string $user_name;
    public int $items_count;
    public int $items_qty;
    public float $grand_total;
    public float $sub_total;
    public string $currency;
    public array $items = [];

    public static function fromModel($model): self
    {
        $dto = new self();
        $dto->id = (string) $model->id;
        $dto->user_email = $model->user_email;
        $dto->user_name = $model->user_name;
        $dto->items_count = (int) $model->items_count;
        $dto->items_qty = (int) $model->items_qty;
        $dto->grand_total = (float) $model->grand_total;
        $dto->sub_total = (float) $model->sub_total;
        $dto->currency = $model->cart_currency_code ?? 'VND';

        if ($model->relationLoaded('items')) {
            $dto->items = $model->items->map(function ($item) {
                return CartItemResponse::fromModel($item);
            })->toArray();
        }

        return $dto;
    }
}

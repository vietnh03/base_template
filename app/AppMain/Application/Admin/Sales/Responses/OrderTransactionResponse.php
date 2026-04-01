<?php

namespace App\AppMain\Application\Admin\Sales\Responses;

use App\AppMain\Core\BaseResponse;
use App\Models\OrderTransaction;

class OrderTransactionResponse
{
    public static function single($model): array
    {
        return [
            'data' => self::formatTransaction($model),
        ];
    }

    public static function paginated($paginator): array
    {
        $data = [];
        foreach ($paginator->items() as $item) {
            $data[] = self::formatTransaction($item);
        }

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]
        ];
    }

    protected static function formatTransaction($model): array
    {
        if (!$model) {
            return [];
        }

        return [
            'id' => $model->id,
            'transaction_id' => $model->transaction_id,
            'status' => $model->status,
            'type' => $model->type,
            'amount' => $model->amount,
            'payment_method' => $model->payment_method,
            'order_id' => $model->order_id,
            'invoice_id' => $model->invoice_id,
        ];
    }
}

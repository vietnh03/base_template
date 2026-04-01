<?php

namespace App\AppMain\Application\Admin\Sales\Responses;

use App\AppMain\Core\BaseResponse;
use App\Models\Invoice;

class InvoiceResponse
{
    public static function single($model): array
    {
        return [
            'data' => self::formatInvoice($model),
        ];
    }

    public static function paginated($paginator): array
    {
        $data = [];
        foreach ($paginator->items() as $item) {
            $data[] = self::formatInvoice($item);
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

    protected static function formatInvoice($model): array
    {
        if (!$model) {
            return [];
        }

        return [
            'id' => $model->id,
            'increment_id' => $model->increment_id,
            'state' => $model->state,
            'grand_total' => $model->grand_total,
            'sub_total' => $model->sub_total,
            'order_id' => $model->order_id,
        ];
    }
}

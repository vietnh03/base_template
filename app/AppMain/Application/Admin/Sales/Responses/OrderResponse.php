<?php

namespace App\AppMain\Application\Admin\Sales\Responses;

class OrderResponse
{
    public static function single($order): array
    {
        return [
            'data' => self::formatOrder($order),
        ];
    }

    public static function paginated($paginator): array
    {
        $data = [];
        foreach ($paginator->items() as $order) {
            $data[] = self::formatOrder($order);
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

    protected static function formatOrder($order): array
    {
        if (!$order) {
            return [];
        }

        $result = $order->toArray();

        // Optionally fetch related items and addresses if loaded
        if ($order->relationLoaded('items')) {
            $result['items'] = $order->items->map(function ($item) {
                return $item->toArray();
            })->toArray();
        }

        if ($order->relationLoaded('addresses')) {
            $result['addresses'] = $order->addresses->map(function ($address) {
                return $address->toArray();
            })->toArray();
        }

        if ($order->relationLoaded('customer')) {
            $result['customer'] = $order->customer ? $order->customer->toArray() : null;
        }

        return $result;
    }
}

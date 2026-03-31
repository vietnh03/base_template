<?php

namespace App\AppMain\Domain\Sales\Services;

use App\AppMain\Domain\Sales\Repositories\OrderRepository;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createFromCart(Cart $cart, array $orderData = []): Order
    {
        return DB::transaction(function () use ($cart, $orderData) {
            $order = $this->orderRepository->create(array_merge([
                'increment_id' => $this->generateIncrementId(),
                'status' => 'pending',
                'is_guest' => $cart->is_guest,
                'customer_email' => $cart->customer_email,
                'customer_first_name' => $cart->customer_first_name,
                'customer_last_name' => $cart->customer_last_name,
                'shipping_method' => $cart->shipping_method,
                'coupon_code' => $cart->coupon_code,
                'is_gift' => $cart->is_gift,
                'total_item_count' => $cart->items_count,
                'total_qty_ordered' => $cart->items_qty,
                'base_currency_code' => $cart->base_currency_code,
                'order_currency_code' => $cart->cart_currency_code,
                'grand_total' => $cart->grand_total,
                'base_grand_total' => $cart->base_grand_total,
                'sub_total' => $cart->sub_total,
                'base_sub_total' => $cart->base_sub_total,
                'tax_amount' => $cart->tax_total,
                'base_tax_amount' => $cart->base_tax_total,
                'discount_amount' => $cart->discount_amount,
                'base_discount_amount' => $cart->base_discount_amount,
                'customer_id' => $cart->customer_id,
                'cart_id' => $cart->id,
            ], $orderData));

            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'sku' => $cartItem->sku,
                    'type' => $cartItem->type,
                    'name' => $cartItem->name,
                    'coupon_code' => $cartItem->coupon_code,
                    'weight' => $cartItem->weight,
                    'total_weight' => $cartItem->total_weight,
                    'qty_ordered' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'base_price' => $cartItem->base_price,
                    'total' => $cartItem->total,
                    'base_total' => $cartItem->base_total,
                    'tax_percent' => $cartItem->tax_percent,
                    'tax_amount' => $cartItem->tax_amount,
                    'base_tax_amount' => $cartItem->base_tax_amount,
                    'discount_percent' => $cartItem->discount_percent,
                    'discount_amount' => $cartItem->discount_amount,
                    'base_discount_amount' => $cartItem->base_discount_amount,
                    'product_id' => $cartItem->product_id,
                    'order_id' => $order->id,
                    'parent_id' => $cartItem->parent_id,
                    'additional' => $cartItem->additional,
                ]);
            }

            // Also copy addresses from cart to order
            foreach ($cart->addresses as $address) {
                $newAddress = $address->replicate();
                $newAddress->address_type = str_replace('cart_', 'order_', $address->address_type);
                $newAddress->cart_id = null;
                $newAddress->order_id = $order->id;
                $newAddress->save();
            }

            return $order;
        });
    }

    public function cancel(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orderRepository->findOrFail($orderId);

            if ($order->status !== 'pending') {
                throw new \Exception("Only pending orders can be canceled.");
            }

            $order->status = 'canceled';
            $order->save();

            // Typically we should return items to stock here
            return true;
        });
    }

    protected function generateIncrementId(): string
    {
        // Simple increment ID generator, can be customized
        return date('Ymd') . rand(1000, 9999);
    }
}

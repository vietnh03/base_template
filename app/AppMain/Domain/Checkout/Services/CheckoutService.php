<?php

namespace App\AppMain\Domain\Checkout\Services;

use App\AppMain\Domain\Sales\Services\OrderService;
use App\Models\Cart;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected CartService $cartService;
    protected OrderService $orderService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function placeOrder(Cart $cart): \App\Models\Order
    {
        // 1. Validation
        if (!$cart->is_active) {
            throw new Exception("Cart is not active.");
        }

        if ($cart->items->isEmpty()) {
            throw new Exception("Cart is empty.");
        }

        // Ideally, check for shipping and billing addresses if physical products.

        return DB::transaction(function () use ($cart) {
            // 2. Recalculate one last time to be safe
            $this->cartService->recalculate($cart);

            // 3. Create the order using OrderService
            $order = $this->orderService->createFromCart($cart);

            // 4. Deactivate the cart
            $cart->is_active = false;
            $cart->save();

            return $order;
        });
    }
}

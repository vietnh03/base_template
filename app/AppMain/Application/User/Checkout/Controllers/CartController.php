<?php

namespace App\AppMain\Application\User\Checkout\Controllers;

use App\AppMain\Application\User\Checkout\Requests\AddCartItemRequest;
use App\AppMain\Application\User\Checkout\Requests\UpdateCartItemRequest;
use App\AppMain\Domain\Checkout\Services\CartService;
use App\AppMain\Domain\Checkout\Services\CheckoutService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function get(Request $request)
    {
        $user = $request->user('api');
        $cart = $this->cartService->getCurrentCart($user ? $user->id : null);

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty', 'data' => null], 404);
        }

        return response()->json([
            'data' => $cart->load('items')
        ]);
    }

    public function add(AddCartItemRequest $request)
    {
        $user = $request->user('api');
        $qty = $request->input('quantity', 1);

        $cart = $this->cartService->addProduct(
            $request->product_id,
            $qty,
            $user ? $user->id : null
        );

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cart
        ]);
    }

    public function update(UpdateCartItemRequest $request, $itemId)
    {
        $cart = $this->cartService->updateItem($itemId, $request->quantity);

        return response()->json([
            'message' => 'Cart updated successfully',
            'data' => $cart
        ]);
    }

    public function remove($itemId)
    {
        $cart = $this->cartService->removeItem($itemId);

        return response()->json([
            'message' => 'Item removed successfully',
            'data' => $cart
        ]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user('api');
        $cart = $this->cartService->getCurrentCart($user ? $user->id : null);

        if (!$cart) {
            return response()->json(['message' => 'No active cart found for checkout'], 404);
        }

        try {
            $order = $this->checkoutService->placeOrder($cart);

            return response()->json([
                'message' => 'Order placed successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

<?php

namespace App\AppMain\Application\Api\Sales\Checkout\Controllers;

use App\AppMain\Application\Api\Sales\Checkout\Requests\AddCartItemRequest;
use App\AppMain\Application\Api\Sales\Checkout\Requests\UpdateCartItemRequest;
use App\AppMain\Application\Api\Sales\Checkout\Responses\CartResponse;
use App\AppMain\Domain\Checkout\Services\CartService;
use App\AppMain\Domain\Checkout\Services\CheckoutService;
use App\AppMain\Core\Controller;
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
        return $this->baseAction(function () use ($request) {
            $user = $request->user('api');
            $cart = $this->cartService->getCurrentCart($user ? $user->id : null);

            if (!$cart) {
                throw new \Exception('Cart is empty', 404);
            }

            return CartResponse::single($cart->load('items'));
        }, 'Cart retrieved successfully');
    }

    public function add(AddCartItemRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $user = $request->user('api');
            $qty = $request->input('quantity', 1);

            return $this->cartService->addProduct(
                $request->product_id,
                $qty,
                $user ? $user->id : null
            );
        }, 'Product added to cart successfully');
    }

    public function update(UpdateCartItemRequest $request, $itemId)
    {
        return $this->baseAction(function () use ($itemId, $request) {
            return $this->cartService->updateItem($itemId, $request->quantity);
        }, 'Cart updated successfully');
    }

    public function remove($itemId)
    {
        return $this->baseAction(function () use ($itemId) {
            return $this->cartService->removeItem($itemId);
        }, 'Item removed successfully');
    }

    public function checkout(Request $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $user = $request->user('api');
            $cart = $this->cartService->getCurrentCart($user ? $user->id : null);

            if (!$cart) {
                throw new \Exception('No active cart found for checkout', 404);
            }

            return $this->checkoutService->placeOrder($cart);
        }, 'Order placed successfully');
    }
}

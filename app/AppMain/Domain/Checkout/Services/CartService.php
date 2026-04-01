<?php

namespace App\AppMain\Domain\Checkout\Services;

use App\AppMain\Domain\Checkout\Repositories\CartRepository;
use App\AppMain\Domain\Checkout\Repositories\CartItemRepository;
use App\AppMain\Domain\Catalog\Repositories\ProductRepository;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected CartRepository $cartRepository;
    protected CartItemRepository $cartItemRepository;
    protected ProductRepository $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        ProductRepository $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->productRepository = $productRepository;
    }

    public function getCurrentCart(?string $customerId = null, ?string $cartId = null): ?Cart
    {
        if ($customerId) {
            return $this->cartRepository->findActiveByCustomerId($customerId);
        }

        if ($cartId) {
            return $this->cartRepository->findActiveByGuestId($cartId);
        }

        return null;
    }

    public function getOrCreateCart(?string $customerId = null, ?string $cartId = null): Cart
    {
        $cart = $this->getCurrentCart($customerId, $cartId);

        if (!$cart) {
            $cart = $this->cartRepository->create([
                'customer_id' => $customerId,
                'is_active' => true,
                'is_guest' => !$customerId,
                'base_currency_code' => config('app.currency', 'VND'),
                'cart_currency_code' => config('app.currency', 'VND'),
                'global_currency_code' => config('app.currency', 'VND'),
            ]);
        }

        return $cart;
    }

    public function addProduct(string $productId, int $qty = 1, ?string $customerId = null, ?string $cartId = null): Cart
    {
        return DB::transaction(function () use ($productId, $qty, $customerId, $cartId) {
            $cart = $this->getOrCreateCart($customerId, $cartId);
            $product = $this->productRepository->findById($productId, ['flat']);

            $flat = $product->flat->first();

            $cartItem = $this->cartItemRepository->getModel()::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $newQty = $cartItem->quantity + $qty;
                $this->cartItemRepository->update($cartItem->id, [
                    'quantity' => $newQty,
                    'total' => $newQty * $cartItem->price,
                    'base_total' => $newQty * $cartItem->base_price,
                    'total_weight' => $newQty * $cartItem->weight,
                    'base_total_weight' => $newQty * $cartItem->weight,
                ]);
            } else {
                $price = $flat ? ($flat->price ?? 0) : 0;
                $weight = $flat ? ($flat->weight ?? 0) : 0;
                $name = $flat ? ($flat->name ?? 'Product ' . $product->id) : 'Product ' . $product->id;

                $this->cartItemRepository->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $name,
                    'quantity' => $qty,
                    'price' => $price,
                    'base_price' => $price,
                    'total' => $price * $qty,
                    'base_total' => $price * $qty,
                    'weight' => $weight,
                    'total_weight' => $weight * $qty,
                    'base_total_weight' => $weight * $qty,
                ]);
            }

            return $this->recalculate($cart->fresh());
        });
    }

    public function updateItem(string $itemId, int $qty): Cart
    {
        return DB::transaction(function () use ($itemId, $qty) {
            $cartItem = $this->cartItemRepository->findOrFail($itemId);

            if ($qty <= 0) {
                return $this->removeItem($itemId);
            }

            $this->cartItemRepository->update($itemId, [
                'quantity' => $qty,
                'total' => $qty * $cartItem->price,
                'base_total' => $qty * $cartItem->base_price,
                'total_weight' => $qty * $cartItem->weight,
                'base_total_weight' => $qty * $cartItem->weight,
            ]);

            return $this->recalculate($cartItem->cart->fresh());
        });
    }

    public function removeItem(string $itemId): Cart
    {
        return DB::transaction(function () use ($itemId) {
            $cartItem = $this->cartItemRepository->findOrFail($itemId);
            $cart = $cartItem->cart;
            $this->cartItemRepository->delete($itemId);

            return $this->recalculate($cart->fresh());
        });
    }

    public function recalculate(Cart $cart): Cart
    {
        $items = $cart->items()->get();

        $itemsCount = $items->count();
        $itemsQty = $items->sum('quantity');
        $subTotal = $items->sum('total');
        $baseSubTotal = $items->sum('base_total');

        // Assuming no shipping/tax logic for the basic setup
        $grandTotal = $subTotal;
        $baseGrandTotal = $baseSubTotal;

        $this->cartRepository->update($cart->id, [
            'items_count' => $itemsCount,
            'items_qty' => $itemsQty,
            'sub_total' => $subTotal,
            'base_sub_total' => $baseSubTotal,
            'grand_total' => $grandTotal,
            'base_grand_total' => $baseGrandTotal,
        ]);

        return $cart->fresh('items');
    }

    public function deactivateCart(string $cartId): bool
    {
        $this->cartRepository->update($cartId, ['is_active' => false]);
        return true;
    }
}

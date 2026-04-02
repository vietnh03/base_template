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

    public function getCurrentCart(?string $userId = null, ?string $cartId = null): ?Cart
    {
        if ($userId) {
            return $this->cartRepository->findActiveByUserId($userId);
        }

        if ($cartId) {
            return $this->cartRepository->findActiveByGuestId($cartId);
        }

        return null;
    }

    public function getOrCreateCart(?string $userId = null, ?string $cartId = null): Cart
    {
        $cart = $this->getCurrentCart($userId, $cartId);

        if (!$cart) {
            $cart = $this->cartRepository->create([
                'user_id' => $userId,
                'is_active' => true,
                'is_guest' => !$userId,
                'base_currency_code' => config('app.currency', 'VND'),
                'cart_currency_code' => config('app.currency', 'VND'),
                'global_currency_code' => config('app.currency', 'VND'),
            ]);
        }

        return $cart;
    }

    public function addProduct(string $productId, int $qty = 1, ?string $userId = null, ?string $cartId = null): Cart
    {
        return DB::transaction(function () use ($productId, $qty, $userId, $cartId) {
            if ($qty <= 0) {
                throw new \InvalidArgumentException('Quantity must be positive');
            }

            $cart = $this->getOrCreateCart($userId, $cartId);

            // Row-level lock on the cart to prevent race conditions during item addition
            $this->cartRepository->getModel()::where('id', $cart->id)->lockForUpdate()->first();

            $product = $this->productRepository->findById($productId, ['flat']);

            $flat = $product->flat->first();

            $inventory = \App\Models\ProductInventory::where('product_id', $productId)->first();
            $availableQty = $inventory ? $inventory->qty : 0;

            $cartItem = $this->cartItemRepository->getModel()::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            $newQty = $cartItem ? $cartItem->quantity + $qty : $qty;
            if ($newQty > $availableQty) {
                throw new \Exception("Not enough stock available for this product. Available: {$availableQty}");
            }

            if ($cartItem) {
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

            return $this->recalculate($cart);
        });
    }

    public function updateItem(string $itemId, int $qty): Cart
    {
        return DB::transaction(function () use ($itemId, $qty) {
            if ($qty < 0) {
                throw new \InvalidArgumentException('Quantity cannot be negative');
            }

            $cartItem = $this->cartItemRepository->findById($itemId);

            if ($qty <= 0) {
                return $this->removeItem($itemId);
            }

            $inventory = \App\Models\ProductInventory::where('product_id', $cartItem->product_id)->first();
            $availableQty = $inventory ? $inventory->qty : 0;
            if ($qty > $availableQty) {
                throw new \Exception("Not enough stock available for this product. Available: {$availableQty}");
            }

            $this->cartItemRepository->update($itemId, [
                'quantity' => $qty,
                'total' => $qty * $cartItem->price,
                'base_total' => $qty * $cartItem->base_price,
                'total_weight' => $qty * $cartItem->weight,
                'base_total_weight' => $qty * $cartItem->weight,
            ]);

            return $this->recalculate($cartItem->cart);
        });
    }

    public function removeItem(string $itemId): Cart
    {
        return DB::transaction(function () use ($itemId) {
            $cartItem = $this->cartItemRepository->findById($itemId);
            $cart = $cartItem->cart;
            $this->cartItemRepository->delete($itemId);

            return $this->recalculate($cart);
        });
    }

    public function recalculate(Cart $cart): Cart
    {
        $cart->load('items');
        $items = $cart->items;

        $itemsCount = $items->count();
        $itemsQty = $items->sum('quantity');
        $subTotal = $items->sum('total');
        $baseSubTotal = $items->sum('base_total');

        // Assuming no shipping/tax logic for the basic setup
        $grandTotal = $subTotal;
        $baseGrandTotal = $baseSubTotal;

        $updates = [
            'items_count' => $itemsCount,
            'items_qty' => $itemsQty,
            'sub_total' => $subTotal,
            'base_sub_total' => $baseSubTotal,
            'grand_total' => $grandTotal,
            'base_grand_total' => $baseGrandTotal,
        ];

        $this->cartRepository->update($cart->id, $updates);

        foreach ($updates as $key => $val) {
            $cart->{$key} = $val;
        }

        return $cart;
    }

    public function deactivateCart(string $cartId): bool
    {
        $this->cartRepository->update($cartId, ['is_active' => false]);
        return true;
    }
}

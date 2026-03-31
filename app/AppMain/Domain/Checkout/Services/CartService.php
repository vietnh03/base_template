<?php

namespace App\AppMain\Domain\Checkout\Services;

use App\AppMain\Domain\Checkout\Repositories\CartRepository;
use App\AppMain\Domain\Checkout\Repositories\CartItemRepository;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected CartRepository $cartRepository;
    protected CartItemRepository $cartItemRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    public function getCurrentCart(?int $customerId = null, ?int $cartId = null): ?Cart
    {
        if ($customerId) {
            return $this->cartRepository->findActiveByCustomerId($customerId);
        }

        if ($cartId) {
            return $this->cartRepository->findActiveByGuestId($cartId);
        }

        return null;
    }

    public function getOrCreateCart(?int $customerId = null, ?int $cartId = null): Cart
    {
        $cart = $this->getCurrentCart($customerId, $cartId);

        if (!$cart) {
            $cart = $this->cartRepository->create([
                'customer_id' => $customerId,
                'is_active' => true,
                'is_guest' => !$customerId,
                'base_currency_code' => 'USD',
                'cart_currency_code' => 'USD',
                'global_currency_code' => 'USD',
            ]);
        }

        return $cart;
    }

    public function addProduct(int $productId, int $qty = 1, ?int $customerId = null, ?int $cartId = null): Cart
    {
        return DB::transaction(function () use ($productId, $qty, $customerId, $cartId) {
            $cart = $this->getOrCreateCart($customerId, $cartId);
            $product = Product::findOrFail($productId);

            $flat = \App\Models\ProductFlat::where('product_id', $product->id)->first();

            $cartItem = $this->cartItemRepository->getModel()::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $qty;
                $cartItem->total = $cartItem->quantity * $cartItem->price;
                $cartItem->base_total = $cartItem->quantity * $cartItem->base_price;
                $cartItem->total_weight = $cartItem->quantity * $cartItem->weight;
                $cartItem->base_total_weight = $cartItem->quantity * $cartItem->weight;
                $cartItem->save();
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

    public function updateItem(int $itemId, int $qty): Cart
    {
        return DB::transaction(function () use ($itemId, $qty) {
            $cartItem = $this->cartItemRepository->findOrFail($itemId);

            if ($qty <= 0) {
                return $this->removeItem($itemId);
            }

            $cartItem->quantity = $qty;
            $cartItem->total = $cartItem->quantity * $cartItem->price;
            $cartItem->base_total = $cartItem->quantity * $cartItem->base_price;
            $cartItem->total_weight = $cartItem->quantity * $cartItem->weight;
            $cartItem->base_total_weight = $cartItem->quantity * $cartItem->weight;
            $cartItem->save();

            return $this->recalculate($cartItem->cart);
        });
    }

    public function removeItem(int $itemId): Cart
    {
        return DB::transaction(function () use ($itemId) {
            $cartItem = $this->cartItemRepository->findOrFail($itemId);
            $cart = $cartItem->cart;
            $cartItem->delete();

            return $this->recalculate($cart);
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

        $cart->update([
            'items_count' => $itemsCount,
            'items_qty' => $itemsQty,
            'sub_total' => $subTotal,
            'base_sub_total' => $baseSubTotal,
            'grand_total' => $grandTotal,
            'base_grand_total' => $baseGrandTotal,
        ]);

        return $cart->fresh('items');
    }
}

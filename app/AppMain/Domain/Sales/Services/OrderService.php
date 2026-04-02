<?php

namespace App\AppMain\Domain\Sales\Services;

use App\AppMain\Domain\Core\Repositories\AddressRepository;
use App\AppMain\Domain\Sales\Repositories\OrderRepository;
use App\AppMain\Domain\Sales\Repositories\OrderItemRepository;
use App\AppMain\Domain\Catalog\Repositories\ProductRepository;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected OrderItemRepository $orderItemRepository;
    protected AddressRepository $addressRepository;
    protected ProductRepository $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        AddressRepository $addressRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
    }

    public function getOrdersWithFilters(array $filters)
    {
        return $this->orderRepository->getOrdersWithFilters($filters);
    }

    public function findOrder($id, array $with = ['items', 'addresses', 'user'])
    {
        $query = $this->orderRepository->getModel()::with($with);
        return $query->findOrFail($id);
    }

    public function updateStatus($id, string $status, ?string $comment = null)
    {
        return DB::transaction(function () use ($id, $status, $comment) {
            $order = $this->findOrder($id, []);

            if ($order->status === $status) {
                return $order;
            }

            $this->orderRepository->update($id, ['status' => $status]);

            // Comment saving logic if we add order_comments later

            return $this->findOrder($id, []);
        });
    }

    public function createFromCart(Cart $cart, array $orderData = []): Order
    {
        return DB::transaction(function () use ($cart, $orderData) {
            $order = $this->orderRepository->create(array_merge([
                'increment_id' => $this->generateIncrementId(),
                'status' => 'pending',
                'is_guest' => $cart->is_guest,
                'user_email' => $cart->user_email,
                'user_name' => $cart->user_name,
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
                'user_id' => $cart->user_id,
                'cart_id' => $cart->id,
            ], $orderData));

            // Deadlock prevention: sort product IDs before locked query
            $productIds = $cart->items->pluck('product_id')->sort()->values()->all();
            $inventories = \App\Models\ProductInventory::whereIn('product_id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $orderItemsData = [];
            $now = now();

            foreach ($cart->items as $cartItem) {
                $inventory = $inventories->get($cartItem->product_id);
                if (!$inventory || $inventory->qty < $cartItem->quantity) {
                    throw new \Exception("Product {$cartItem->name} is out of stock or insufficient quantity.");
                }
                $inventory->qty -= $cartItem->quantity;
                $inventory->save();

                $orderItemsData[] = [
                    'id' => (string) Str::uuid(),
                    'sku' => $cartItem->sku,
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
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($orderItemsData)) {
                $this->orderItemRepository->getModel()::insert($orderItemsData);
            }

            // Also copy addresses from cart to order
            $addressesData = [];
            foreach ($cart->addresses as $address) {
                $addressData = $address->toArray();
                $addressData['id'] = (string) Str::uuid();
                unset($addressData['created_at'], $addressData['updated_at']);
                $addressData['address_type'] = str_replace('cart_', 'order_', $addressData['address_type']);
                $addressData['cart_id'] = null;
                $addressData['order_id'] = $order->id;
                $addressData['created_at'] = $now;
                $addressData['updated_at'] = $now;

                $addressesData[] = $addressData;
            }
            if (!empty($addressesData)) {
                $this->addressRepository->getModel()::insert($addressesData);
            }

            $order->load(['items', 'addresses', 'user']);
            return $order;
        });
    }

    public function createDirectOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $grandTotal = 0;
            $subTotal = 0;
            $totalQty = 0;
            $totalWeight = 0;

            $itemsData = [];

            // 1. Process items and calculate totals
            $productIds = collect($data['items'])->pluck('product_id')->sort()->values()->all();
            $products = $this->productRepository->getModel()::with('flat')->whereIn('id', $productIds)->get()->keyBy('id');
            // Deal with inventory locks safely
            $inventories = \App\Models\ProductInventory::whereIn('product_id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            foreach ($data['items'] as $itemInput) {
                $product = $products->get($itemInput['product_id']);
                if (!$product) {
                    throw new \Exception("Product with ID {$itemInput['product_id']} not found.");
                }

                $flat = $product->flat->first();

                $price = $flat ? ($flat->price ?? 0) : 0;
                $weight = $flat ? ($flat->weight ?? 0) : 0;
                $name = $flat ? ($flat->name ?? 'Product ' . $product->id) : 'Product ' . $product->id;

                $qty = $itemInput['quantity'];
                if ($qty <= 0) {
                    throw new \InvalidArgumentException('Quantity must be positive');
                }

                $inventory = $inventories->get($product->id);
                if (!$inventory || $inventory->qty < $qty) {
                    throw new \Exception("Product {$product->sku} is out of stock or insufficient quantity.");
                }
                $inventory->qty -= $qty;
                $inventory->save();

                $total = $price * $qty;

                $grandTotal += $total;
                $subTotal += $total;
                $totalQty += $qty;
                $totalWeight += ($weight * $qty);

                $itemsData[] = [
                    'id' => (string) Str::uuid(),
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $name,
                    'weight' => $weight,
                    'total_weight' => $weight * $qty,
                    'qty_ordered' => $qty,
                    'price' => $price,
                    'base_price' => $price,
                    'total' => $total,
                    'base_total' => $total,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'base_tax_amount' => 0,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'base_discount_amount' => 0,
                ];
            }

            // 2. Create Order
            $order = $this->orderRepository->create([
                'increment_id' => $this->generateIncrementId(),
                'status' => 'pending',
                'is_guest' => empty($data['user_id']),
                'user_email' => $data['user_email'],
                'user_name' => $data['user_name'],
                'user_id' => $data['user_id'] ?? null,
                'shipping_method' => $data['shipping_method'] ?? null,
                'coupon_code' => $data['coupon_code'] ?? null,
                'is_gift' => false,
                'total_item_count' => count($itemsData),
                'total_qty_ordered' => $totalQty,
                'base_currency_code' => config('app.currency', 'VND'),
                'order_currency_code' => config('app.currency', 'VND'),
                'grand_total' => $grandTotal,
                'base_grand_total' => $grandTotal,
                'sub_total' => $subTotal,
                'base_sub_total' => $subTotal,
                'tax_amount' => 0,
                'base_tax_amount' => 0,
                'discount_amount' => 0,
                'base_discount_amount' => 0,
            ]);

            // 3. Create Order Items
            $now = now();
            foreach ($itemsData as &$itemInfo) {
                $itemInfo['order_id'] = $order->id;
                $itemInfo['created_at'] = $now;
                $itemInfo['updated_at'] = $now;
            }
            if (!empty($itemsData)) {
                $this->orderItemRepository->getModel()::insert($itemsData);
            }

            // 4. Create Addresses
            $addressesData = [];
            $shippingAddress = $data['shipping_address'];
            $shippingAddress['address_type'] = 'order_shipping';
            $shippingAddress['id'] = (string) Str::uuid();
            $shippingAddress['order_id'] = $order->id;
            $shippingAddress['created_at'] = $now;
            $shippingAddress['updated_at'] = $now;
            $addressesData[] = $shippingAddress;

            $billingAddress = $data['billing_address'];
            $billingAddress['address_type'] = 'order_billing';
            $billingAddress['id'] = (string) Str::uuid();
            $billingAddress['order_id'] = $order->id;
            $billingAddress['created_at'] = $now;
            $billingAddress['updated_at'] = $now;
            $addressesData[] = $billingAddress;

            if (!empty($addressesData)) {
                $this->addressRepository->getModel()::insert($addressesData);
            }

            $order->load(['items', 'addresses', 'user']);
            return $order;
        });
    }

    public function cancel(string $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orderRepository->findById($orderId);

            if (!$order) {
                throw new \Exception("Order not found.");
            }

            if ($order->status !== 'pending') {
                throw new \Exception("Only pending orders can be canceled.");
            }

            $this->orderRepository->update($orderId, ['status' => 'canceled']);

            // Restock items
            $productIds = collect($order->items)->pluck('product_id')->sort()->values()->all();
            if (!empty($productIds)) {
                $inventories = \App\Models\ProductInventory::whereIn('product_id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');

                foreach ($order->items as $item) {
                    if ($inventory = $inventories->get($item->product_id)) {
                        $inventory->qty += $item->qty_ordered;
                        $inventory->save();
                    }
                }
            }

            return true;
        });
    }

    protected function generateIncrementId(): string
    {
        $prefix = date('Ymd');

        // Retry a few times if collision occurs (though random 6 is 1/68B, let's be safe)
        for ($i = 0; $i < 5; $i++) {
            $id = $prefix . strtoupper(\Illuminate\Support\Str::random(6));
            if (!$this->orderRepository->getModel()::where('increment_id', $id)->exists()) {
                return $id;
            }
        }

        return $prefix . strtoupper(\Illuminate\Support\Str::random(10));
    }
}

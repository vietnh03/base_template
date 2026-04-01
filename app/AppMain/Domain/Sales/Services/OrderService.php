<?php

namespace App\AppMain\Domain\Sales\Services;

use App\AppMain\Domain\Core\Repositories\AddressRepository;
use App\AppMain\Domain\Sales\Repositories\OrderRepository;
use App\AppMain\Domain\Sales\Repositories\OrderItemRepository;
use App\AppMain\Domain\Catalog\Repositories\ProductRepository;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

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

    public function findOrder($id, array $with = ['items', 'addresses', 'customer'])
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
                $this->orderItemRepository->create([
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
                ]);
            }

            // Also copy addresses from cart to order
            foreach ($cart->addresses as $address) {
                $addressData = $address->toArray();
                unset($addressData['id'], $addressData['created_at'], $addressData['updated_at']);
                $addressData['address_type'] = str_replace('cart_', 'order_', $addressData['address_type']);
                $addressData['cart_id'] = null;
                $addressData['order_id'] = $order->id;

                $this->addressRepository->create($addressData);
            }

            return $this->findOrder($order->id, []);
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
            foreach ($data['items'] as $itemInput) {
                $product = $this->productRepository->findById($itemInput['product_id'], ['flat']);
                $flat = $product->flat->first();

                $price = $flat ? ($flat->price ?? 0) : 0;
                $weight = $flat ? ($flat->weight ?? 0) : 0;
                $name = $flat ? ($flat->name ?? 'Product ' . $product->id) : 'Product ' . $product->id;

                $qty = $itemInput['quantity'];

                $total = $price * $qty;

                $grandTotal += $total;
                $subTotal += $total;
                $totalQty += $qty;
                $totalWeight += ($weight * $qty);

                $itemsData[] = [
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
                'is_guest' => empty($data['customer_id']),
                'customer_email' => $data['customer_email'],
                'customer_first_name' => $data['customer_first_name'],
                'customer_last_name' => $data['customer_last_name'],
                'customer_id' => $data['customer_id'] ?? null,
                'shipping_method' => $data['shipping_method'] ?? null,
                'coupon_code' => $data['coupon_code'] ?? null,
                'is_gift' => false,
                'total_item_count' => count($itemsData),
                'total_qty_ordered' => $totalQty,
                'base_currency_code' => 'USD',
                'order_currency_code' => 'USD',
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
            foreach ($itemsData as $itemInfo) {
                $itemInfo['order_id'] = $order->id;
                $this->orderItemRepository->create($itemInfo);
            }

            // 4. Create Addresses
            $shippingAddress = $data['shipping_address'];
            $shippingAddress['address_type'] = 'order_shipping';
            $shippingAddress['order_id'] = $order->id;
            $this->addressRepository->create($shippingAddress);

            $billingAddress = $data['billing_address'];
            $billingAddress['address_type'] = 'order_billing';
            $billingAddress['order_id'] = $order->id;
            $this->addressRepository->create($billingAddress);

            return $this->findOrder($order->id, []);
        });
    }

    public function cancel(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->orderRepository->findOrFail($orderId);

            if ($order->status !== 'pending') {
                throw new \Exception("Only pending orders can be canceled.");
            }

            $this->orderRepository->update($orderId, ['status' => 'canceled']);

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

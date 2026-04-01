<?php

namespace App\AppMain\Domain\Sales\Services;

use App\AppMain\Domain\Sales\Repositories\InvoiceRepository;
use App\AppMain\Domain\Sales\Repositories\InvoiceItemRepository;
use App\AppMain\Domain\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    protected InvoiceRepository $invoiceRepository;
    protected InvoiceItemRepository $invoiceItemRepository;
    protected OrderRepository $orderRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceItemRepository $invoiceItemRepository,
        OrderRepository $orderRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getAll(array $filters = [])
    {
        $query = $this->invoiceRepository->getModel()::query();
        return $query->paginate($filters['limit'] ?? 10);
    }

    public function findById($id, array $with = ['order'])
    {
        return $this->invoiceRepository->getModel()::with($with)->findOrFail($id);
    }

    public function createForOrder(string $orderId, array $data = [])
    {
        return DB::transaction(function () use ($orderId, $data) {
            $order = $this->orderRepository->findOrFail($orderId);

            // Calculate totals from order if not provided
            $grandTotal = $data['grand_total'] ?? $order->grand_total;
            $subTotal = $data['sub_total'] ?? $order->sub_total;

            $invoice = $this->invoiceRepository->create(array_merge([
                'order_id' => $order->id,
                'increment_id' => $this->generateIncrementId(),
                'state' => 'paid',
                'email_sent' => false,
                'total_qty' => $order->total_qty_ordered,
                'base_currency_code' => $order->base_currency_code,
                'invoice_currency_code' => $order->order_currency_code,
                'order_currency_code' => $order->order_currency_code,
                'sub_total' => $subTotal,
                'base_sub_total' => $data['base_sub_total'] ?? $order->base_sub_total,
                'grand_total' => $grandTotal,
                'base_grand_total' => $data['base_grand_total'] ?? $order->base_grand_total,
                'shipping_amount' => $order->shipping_amount,
                'base_shipping_amount' => $order->base_shipping_amount,
                'tax_amount' => $order->tax_amount,
                'base_tax_amount' => $order->base_tax_amount,
                'discount_amount' => $order->discount_amount,
                'base_discount_amount' => $order->base_discount_amount,
                'order_address_id' => null, // Would map to billing address ideally
                'transaction_id' => $data['transaction_id'] ?? null,
            ], $data));

            // Create invoice items based on order items
            $orderItems = $order->items;
            $invoiceItemsData = [];
            $now = now();

            foreach ($orderItems as $item) {
                $invoiceItemsData[] = [
                    'invoice_id' => $invoice->id,
                    'order_item_id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'qty' => $item->qty_ordered, // assuming full invoice
                    'price' => $item->price,
                    'base_price' => $item->base_price,
                    'total' => $item->total,
                    'base_total' => $item->base_total,
                    'tax_amount' => $item->tax_amount,
                    'base_tax_amount' => $item->base_tax_amount,
                    'product_id' => $item->product_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($invoiceItemsData)) {
                $this->invoiceItemRepository->getModel()::insert($invoiceItemsData);
            }

            $invoice->load(['order']);
            return $invoice;
        });
    }

    protected function generateIncrementId(): string
    {
        return 'INV-' . date('Ymd') . strtoupper(\Illuminate\Support\Str::random(6));
    }
}

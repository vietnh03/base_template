<?php

namespace App\AppMain\Application\Admin\Sales\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Sales\Services\OrderService;
use App\AppMain\Application\Admin\Sales\Requests\OrderStatusRequest;
use App\AppMain\Application\Admin\Sales\Requests\OrderFilter;
use App\AppMain\Application\Admin\Sales\Requests\OrderStoreRequest;
use App\AppMain\Application\Admin\Sales\Responses\OrderResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filter = OrderFilter::fromRequest($request);
            $request->validate($filter->validate());
            $orders = $this->orderService->getOrdersWithFilters($filter->toArray());
            return OrderResponse::paginated($orders);
        }, 'Orders retrieved successfully');
    }

    public function store(OrderStoreRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $order = $this->orderService->createDirectOrder($request->validated());
            return OrderResponse::single($order);
        }, 'Order created successfully', 201);
    }

    public function show(string $id)
    {
        $order = $this->orderService->findOrder($id);

        return $this->baseAction(function () use ($order) {
            return OrderResponse::single($order);
        }, 'Order retrieved successfully');
    }

    public function updateStatus(OrderStatusRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            $this->orderService->updateStatus($id, $request->validated('status'), $request->validated('comment'));
            $updatedOrder = $this->orderService->findOrder($id);
            return OrderResponse::single($updatedOrder);
        }, 'Order status updated successfully');
    }

    public function cancel(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $this->orderService->cancel((int) $id);
            return ['message' => 'Order canceled successfully'];
        }, 'Order canceled successfully');
    }
}

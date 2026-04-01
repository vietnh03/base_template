<?php

namespace App\AppMain\Application\Admin\Sales\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Sales\Services\InvoiceService;
use App\AppMain\Application\Admin\Sales\Responses\InvoiceResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $invoices = $this->invoiceService->getAll($request->all());
            return InvoiceResponse::paginated($invoices);
        }, 'Invoices retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string|exists:orders,id',
            'grand_total' => 'nullable|numeric',
        ]);

        return $this->baseActionTransaction(function () use ($request) {
            $invoice = $this->invoiceService->createForOrder($request->order_id, $request->except('order_id'));
            return InvoiceResponse::single($invoice);
        }, 'Invoice created successfully', 201);
    }

    public function show($id)
    {
        return $this->baseAction(function () use ($id) {
            $invoice = $this->invoiceService->findById($id);
            return InvoiceResponse::single($invoice);
        }, 'Invoice retrieved successfully');
    }
}

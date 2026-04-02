<?php

namespace App\AppMain\Application\Admin\Sales\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Sales\Services\OrderTransactionService;
use App\AppMain\Application\Admin\Sales\Requests\StoreTransactionRequest;
use App\AppMain\Application\Admin\Sales\Responses\OrderTransactionResponse;
use Illuminate\Http\Request;

class OrderTransactionController extends Controller
{
    protected OrderTransactionService $transactionService;

    public function __construct(OrderTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $transactions = $this->transactionService->getAll($request->all());
            return OrderTransactionResponse::paginated($transactions);
        }, 'Transactions retrieved successfully');
    }

    public function store(StoreTransactionRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            return $this->transactionService->create($request->all());
        }, 'Transaction created successfully');
    }

    public function show($id)
    {
        return $this->baseAction(function () use ($id) {
            $transaction = $this->transactionService->findById($id);
            return OrderTransactionResponse::single($transaction);
        }, 'Transaction retrieved successfully');
    }
}

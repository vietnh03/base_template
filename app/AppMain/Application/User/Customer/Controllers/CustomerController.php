<?php

namespace App\AppMain\Application\User\Customer\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\Customer\DTOs\CustomerDTO;
use App\AppMain\Application\User\Customer\Requests\CustomerFilter;
use App\AppMain\Application\User\Customer\Responses\CustomerResponse;
use App\AppMain\Application\User\Customer\Requests\CustomerRequest;
use App\AppMain\Domain\Customer\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            // Create filter from request
            $filter = CustomerFilter::fromRequest($request);

            // Validate filter data
            $request->validate($filter->validate());

            // Get customers with filters (pass as array to Domain Service)
            $customers = $this->customerService->getCustomersWithFilters($filter->toArray());

            // Transform to Response (hides sensitive fields like notes)
            return CustomerResponse::paginated($customers);
        }, 'Customers retrieved successfully', 'Failed to retrieve customers');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            // Find customer by ID
            $customer = $this->customerService->findCustomer($id);

            // Throw exception if not found (baseAction will handle it)
            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            // Transform to Response
            return CustomerResponse::single($customer);
        }, 'Customer retrieved successfully', 'Failed to retrieve customer');
    }

    public function store(CustomerRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            // Create DTO from validated data
            $dto = CustomerDTO::fromRequest($request);

            // Create customer
            $customer = $this->customerService->createCustomer($dto);

            // Transform to Response
            return CustomerResponse::single($customer);
        }, 'Customer created successfully', 'Failed to create customer');
    }

    public function update(CustomerRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            // Check if customer exists
            $customer = $this->customerService->findCustomer($id);
            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            // Create DTO from validated data
            $dto = CustomerDTO::fromRequest($request);

            // Update customer
            $this->customerService->updateCustomer($id, $dto);

            // Return updated customer
            $updatedCustomer = $this->customerService->findCustomer($id);
            return CustomerResponse::single($updatedCustomer);
        }, 'Customer updated successfully', 'Failed to update customer');
    }

    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            // Check if customer exists
            $customer = $this->customerService->findCustomer($id);
            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            // Delete customer
            $this->customerService->deleteCustomer($id);

            // Return success message
            return ['message' => 'Customer deleted successfully'];
        }, 'Customer deleted successfully', 'Failed to delete customer');
    }
}

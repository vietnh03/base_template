<?php

namespace App\AppMain\Domain\Customer\Services;

use App\AppMain\Domain\Customer\DTOs\CustomerDTO;
use App\AppMain\Domain\Customer\Repositories\CustomerRepository;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get customers with filters (paginated)
     * Accepts filter data as array for flexibility across different application layers
     */
    public function getCustomersWithFilters(array $filters)
    {
        return $this->customerRepository->getCustomersWithFilters($filters);
    }

    /**
     * Find customer by ID
     */
    public function findCustomer(string $id)
    {
        return $this->customerRepository->findById($id);
    }

    /**
     * Create new customer
     */
    public function createCustomer(CustomerDTO $dto)
    {
        // Use onlyFilled() to avoid passing null values
        return $this->customerRepository->create($dto->onlyFilled());
    }

    /**
     * Update customer
     */
    public function updateCustomer(string $id, CustomerDTO $dto)
    {
        // Use onlyFilled() for partial updates
        return $this->customerRepository->update($id, $dto->onlyFilled());
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(string $id): bool
    {
        return $this->customerRepository->delete($id);
    }
}
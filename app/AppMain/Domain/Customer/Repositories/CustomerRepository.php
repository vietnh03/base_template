<?php


namespace App\AppMain\Domain\Customer\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    public function getModel()
    {
        return Customer::class;
    }

    /**
     * Get customers with filters (paginated)
     */
    public function getCustomersWithFilters($filters = [])
    {
        $query = $this->newQuery();

        // Apply exact match filters
        $exactFilters = [
            'customer_type' => $filters['customer_type'] ?? null,
            'customer_status' => $filters['customer_status'] ?? null,
            'assigned_staff_id' => $filters['assigned_staff_id'] ?? null,
        ];

        $this->applyExactFilters($query, $exactFilters);

        // Apply LIKE search filters
        $likeFilters = [
            'full_name' => $filters['full_name'] ?? null,
            'phone_number' => $filters['phone_number'] ?? null,
        ];

        $this->applyLikeFilters($query, $likeFilters);

        // Apply date range filters
        $dateFilters = [
            [
                'field' => 'created_at',
                'from' => $filters['created_from'] ?? null,
                'to' => $filters['created_to'] ?? null,
            ],
            [
                'field' => 'updated_at',
                'from' => $filters['updated_from'] ?? null,
                'to' => $filters['updated_to'] ?? null,
            ],
            [
                'field' => 'date_of_birth',
                'exact' => $filters['date_of_birth'] ?? null,
            ]
        ];

        $this->applyDateFilters($query, $dateFilters);

        // Apply global search
        $searchTerm = $filters['search'] ?? null;
        $searchableFields = ['full_name', 'email', 'phone_number'];
        $this->applyGlobalSearch($query, $searchableFields, $searchTerm);

        // Apply sorting
        $this->applySortingFilter($query, $filters);

        // Paginate results
        return $this->applyPagination($query, $filters);
    }
}
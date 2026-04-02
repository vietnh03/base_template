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
            'status' => $filters['status'] ?? null,
            'is_verified' => $filters['is_verified'] ?? null,
        ];

        $this->applyExactFilters($query, $exactFilters);

        // Apply LIKE search filters
        $likeFilters = [
            'name' => $filters['name'] ?? null,
            'phone' => $filters['phone'] ?? null,
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
        $searchableFields = ['name', 'email', 'phone'];
        $this->applyGlobalSearch($query, $searchableFields, $searchTerm);

        // Apply sorting
        $this->applySortingFilter($query, $filters);

        // Paginate results
        return $this->applyPagination($query, $filters);
    }
}
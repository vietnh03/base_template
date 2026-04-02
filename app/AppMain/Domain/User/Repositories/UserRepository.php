<?php

namespace App\AppMain\Domain\User\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }

    /**
     * Get users with filters (paginated)
     */
    public function getUsersWithFilters(array $filters)
    {
        $query = $this->newQuery();

        // Exact filters
        $this->applyExactFilters($query, collect($filters)->only([
            'status',
            'is_verified',
            'gender'
        ])->toArray());

        // Like filters
        $this->applyLikeFilters($query, collect($filters)->only([
            'name',
            'phone',
            'email'
        ])->toArray());

        // Global search
        if (isset($filters['search']) && $filters['search']) {
            $this->applyGlobalSearch($query, ['name', 'phone', 'email'], $filters['search']);
        }

        // Sorting
        $this->applySortingFilter($query, $filters);

        // Pagination
        return $this->applyPagination($query, $filters);
    }
}

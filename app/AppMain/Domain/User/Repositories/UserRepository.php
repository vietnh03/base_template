<?php

namespace App\AppMain\Domain\User\Repositories;

use App\Models\User;

class UserRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find user by ID
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new user
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update user by ID
     */
    public function update($id, array $data): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    /**
     * Delete user by ID
     */
    public function delete($id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    /**
     * Get total users count
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Get users with filters (paginated)
     */
    public function getUsersWithFilters($filters = [])
    {
        $query = $this->model->newQuery();

        // Apply exact match filters
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply LIKE search filters
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        // Apply date range filters
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Apply global search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Paginate
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }
}

<?php

namespace App\AppMain\CMS\User;

use App\AppMain\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get paginated users with filters
     */
    public function getUsers(array $filters = [])
    {
        return $this->userRepository->getUsersWithFilters($filters);
    }

    /**
     * Get user by ID
     */
    public function getUser(string $userId): array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        return ['user' => $user];
    }

    /**
     * Create new user
     */
    public function createUser(array $data): array
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);

        return ['user' => $user];
    }

    /**
     * Update user
     */
    public function updateUser(string $userId, array $data): array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password from update data if not provided
            unset($data['password']);
        }

        $this->userRepository->update($userId, $data);

        // Refresh user data
        $user = $this->userRepository->findById($userId);

        return ['user' => $user];
    }

    /**
     * Delete user
     */
    public function deleteUser(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        return $this->userRepository->delete($userId);
    }

    /**
     * Get total users count
     */
    public function getTotalUsers(): int
    {
        return $this->userRepository->count();
    }
}

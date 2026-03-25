<?php

namespace App\AppMain\Domain\User\Services;

use App\AppMain\Domain\User\DTOs\UserDTO;
use App\AppMain\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get users with filters (paginated)
     */
    public function getUsersWithFilters(array $filters)
    {
        return $this->userRepository->getUsersWithFilters($filters);
    }

    /**
     * Find user by ID
     */
    public function findUser(string $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create new user
     */
    public function createUser(UserDTO $dto)
    {
        $data = $dto->onlyFilled();

        // Hash password before saving
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->create($data);
    }

    /**
     * Update user
     */
    public function updateUser(string $id, UserDTO $dto)
    {
        $data = $dto->onlyFilled();

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        return $this->userRepository->delete($id);
    }
}

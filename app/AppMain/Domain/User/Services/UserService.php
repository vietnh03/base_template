<?php

namespace App\AppMain\Domain\User\Services;

use App\AppMain\Core\BaseService;
use App\AppMain\Domain\User\Repositories\UserRepository;
use App\AppMain\Domain\User\DTOs\UserDTO;

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
        return $this->userRepository->create($dto->onlyFilled());
    }

    /**
     * Update user
     */
    public function updateUser(string $id, UserDTO $dto)
    {
        return $this->userRepository->update($id, $dto->onlyFilled());
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        return $this->userRepository->delete($id);
    }
}

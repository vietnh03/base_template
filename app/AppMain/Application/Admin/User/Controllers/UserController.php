<?php

namespace App\AppMain\Application\Admin\User\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Domain\User\DTOs\UserDTO;
use App\AppMain\Application\Admin\User\Requests\UserFilter;
use App\AppMain\Application\Admin\User\Responses\UserResponse;
use App\AppMain\Application\Admin\User\Requests\UserRequest;
use App\AppMain\Domain\User\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            // Create filter from request
            $filter = UserFilter::fromRequest($request);

            // Validate filter data
            $request->validate($filter->validate());

            // Get users with filters (pass as array to Domain Service)
            $users = $this->userService->getUsersWithFilters($filter->toArray());

            // Transform to Response
            return UserResponse::paginated($users);
        }, 'Users retrieved successfully', 'Failed to retrieve users');
    }

    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            // Find user by ID
            $user = $this->userService->findUser($id);

            // Throw exception if not found (baseAction will handle it)
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Transform to Response
            return UserResponse::single($user);
        }, 'User retrieved successfully', 'Failed to retrieve user');
    }

    public function store(UserRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            // Create DTO from validated data
            $dto = UserDTO::fromRequest($request);

            // Create user
            $user = $this->userService->createUser($dto);

            // Transform to Response
            return UserResponse::single($user);
        }, 'User created successfully', 'Failed to create user');
    }

    public function update(UserRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            // Check if user exists
            $user = $this->userService->findUser($id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Create DTO from validated data
            $dto = UserDTO::fromRequest($request);

            // Update user
            $this->userService->updateUser($id, $dto);

            // Return updated user
            $updatedUser = $this->userService->findUser($id);
            return UserResponse::single($updatedUser);
        }, 'User updated successfully', 'Failed to update user');
    }

    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            // Check if user exists
            $user = $this->userService->findUser($id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Delete user
            $this->userService->deleteUser($id);

            // Return success message
            return ['message' => 'User deleted successfully'];
        }, 'User deleted successfully', 'Failed to delete user');
    }
}

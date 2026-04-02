<?php

namespace App\AppMain\Application\Admin\User\Controllers;

use App\AppMain\Application\Admin\User\Requests\UserFilter;
use App\AppMain\Application\Admin\User\Requests\UserRequest;
use App\AppMain\Application\Api\User\Responses\UserResponse;
use App\AppMain\Domain\User\DTOs\UserDTO;
use App\AppMain\Domain\User\Services\UserService;
use App\AppMain\Core\Controller;
use Illuminate\Http\Request;

use function App\AppMain\Core\Helpers\responseJsonSuccess;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index(UserFilter $request)
    {
        return $this->baseAction(function () use ($request) {
            $users = $this->userService->getUsersWithFilters($request->validated());
            return UserResponse::paginated($users);
        }, 'Users retrieved successfully');
    }

    /**
     * Store a newly created user
     */
    public function store(UserRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $user = $this->userService->createUser(UserDTO::fromRequest($request));
            return new UserResponse($user);
        }, 'User created successfully');
    }

    /**
     * Display the specified user
     */
    public function show(string $id)
    {
        return $this->baseAction(function () use ($id) {
            $user = $this->userService->findUser($id);
            return new UserResponse($user);
        }, 'User retrieved successfully');
    }

    /**
     * Update the specified user
     */
    public function update(UserRequest $request, string $id)
    {
        return $this->baseActionTransaction(function () use ($request, $id) {
            $this->userService->updateUser($id, UserDTO::fromRequest($request));
            return null;
        }, 'User updated successfully');
    }

    /**
     * Remove the specified user
     */
    public function destroy(string $id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $this->userService->deleteUser($id);
            return null;
        }, 'User deleted successfully');
    }
}

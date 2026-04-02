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
        $users = $this->userService->getUsersWithFilters($request->validated());
        return responseJsonSuccess(UserResponse::paginated($users));
    }

    /**
     * Store a newly created user
     */
    public function store(UserRequest $request)
    {
        $user = $this->userService->createUser(UserDTO::fromRequest($request));
        return responseJsonSuccess(new UserResponse($user), 'User created successfully');
    }

    /**
     * Display the specified user
     */
    public function show(string $id)
    {
        $user = $this->userService->findUser($id);
        return responseJsonSuccess(new UserResponse($user));
    }

    /**
     * Update the specified user
     */
    public function update(UserRequest $request, string $id)
    {
        $this->userService->updateUser($id, UserDTO::fromRequest($request));
        return responseJsonSuccess(null, 'User updated successfully');
    }

    /**
     * Remove the specified user
     */
    public function destroy(string $id)
    {
        $this->userService->deleteUser($id);
        return responseJsonSuccess(null, 'User deleted successfully');
    }
}

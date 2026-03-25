<?php

namespace App\AppMain\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display user list page
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
            'per_page' => $request->get('per_page', 15),
        ];

        $users = $this->userService->getUsers($filters);

        return view('admin.user.index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $this->userService->createUser($validated);

            return redirect()->route('admin.user.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Show edit user form
     */
    public function edit(string $userId)
    {
        try {
            $result = $this->userService->getUser($userId);

            return view('admin.user.edit', [
                'user' => $result['user'],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')
                ->with('error', 'User not found');
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, string $userId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $this->userService->updateUser($userId, $validated);

            return redirect()->route('admin.user.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroy(string $userId)
    {
        try {
            $this->userService->deleteUser($userId);

            return redirect()->route('admin.user.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}

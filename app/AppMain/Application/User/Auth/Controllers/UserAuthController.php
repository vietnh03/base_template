<?php

namespace App\AppMain\Application\User\Auth\Controllers;

use App\AppMain\Application\User\Auth\Requests\UserRegisterRequest;
use App\AppMain\Application\User\Auth\Responses\UserAuthResponse;
use App\AppMain\Domain\Auth\Services\UserAuthService;
use App\AppMain\Core\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function App\AppMain\Core\Helpers\responseJsonFail;
use function App\AppMain\Core\Helpers\responseJsonSuccess;

class UserAuthController extends Controller
{
    protected UserAuthService $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function register(UserRegisterRequest $request)
    {
        try {
            $result = $this->userAuthService->register(
                $request->name,
                $request->email,
                $request->password,
                $request->ip()
            );

            return responseJsonSuccess(UserAuthResponse::fromArray($result), 'Registration successful');
        } catch (\Exception $e) {
            return responseJsonFail($e->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->userAuthService->login(
                $request->email,
                $request->password,
                $request->ip()
            );

            return responseJsonSuccess(UserAuthResponse::fromArray($result), 'Login successful');
        } catch (\Exception $e) {
            return responseJsonFail($e->getMessage(), 401);
        }
    }

    public function me(Request $request)
    {
        return responseJsonSuccess($request->user());
    }

    public function logout(Request $request)
    {
        $this->userAuthService->logout($request->user()->token());
        return responseJsonSuccess(null, 'Logged out successfully');
    }
}

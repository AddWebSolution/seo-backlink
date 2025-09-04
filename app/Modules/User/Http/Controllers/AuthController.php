<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\AuthService;
use App\Modules\User\Http\Requests\LoginUserRequest;
use App\Modules\User\Http\Requests\RegisterUserRequest;


class AuthController extends Controller
{
    public function __construct(protected AuthService $service) {}

    public function register(RegisterUserRequest $request)
    {
        $user = $this->service->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $result = $this->service->login($request->validated());

        return response()->json([
            'message' => 'Login successful',
            'token'   => $result['token'],
        ]);
    }

    public function logout()
    {
        $this->service->logout(auth()->user());

        return response()->json(['message' => 'Logged out successfully']);
    }
}

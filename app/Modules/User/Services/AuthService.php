<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Events\UserLoggedInEvent;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('client');

        event(new UserRegisteredEvent($user));

        return $user;
    }

    /**
     * Attempt login with given credentials and return token.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'auth' => ['Invalid credentials provided.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new UserLoggedInEvent($user));

        return ['token' => $token , 'user' => $user , 'user_type' => $user->getRoleNames()->first() ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}

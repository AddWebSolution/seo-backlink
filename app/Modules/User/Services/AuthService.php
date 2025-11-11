<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Events\UserLoggedInEvent;
use Illuminate\Validation\ValidationException;
use App\Modules\User\Events\UserRegisteredEvent;

class AuthService
{
    /**
     * Register a new client
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);
        $role = Role::find(2);
        $user->assignRole( $role);

        event(new UserRegisteredEvent($user));

        return $user;
    }

    /**
     * Register a new user
     */
    public function userRegister(array $data): User
    {   
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'role'    => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::find($data['role']);
        $user->assignRole($role);

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

//        if (!$user->hasRole('super_admin')) {
//            $user->assignRole('client');
//        }

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new UserLoggedInEvent($user));

        return [
            'token' => $token,
            'user' => $user,
            'user_type' => $user->getPermissionsViaRoles(),
        ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}

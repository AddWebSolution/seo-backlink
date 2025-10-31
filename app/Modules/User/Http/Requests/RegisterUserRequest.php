<?php

namespace App\Modules\User\Http\Requests;

use Addweb\Base\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;


class RegisterUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true; // adjust if you want auth-based restriction
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required'
        ];
    }
}

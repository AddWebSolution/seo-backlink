<?php

namespace App\Modules\User\Http\Requests;

use Addweb\Base\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;


class LoginUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
        ];
    }
}

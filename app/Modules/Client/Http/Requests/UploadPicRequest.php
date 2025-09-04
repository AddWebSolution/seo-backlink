<?php

namespace App\Modules\Client\Http\Requests;

use Addweb\Base\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UploadPicRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true; // you can add auth logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'profile_pic' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}

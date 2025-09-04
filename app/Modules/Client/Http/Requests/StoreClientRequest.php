<?php

namespace App\Modules\Client\Http\Requests;

use Addweb\Base\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreClientRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            //
        ];
    }
}

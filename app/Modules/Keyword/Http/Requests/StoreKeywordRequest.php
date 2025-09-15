<?php

namespace App\Modules\Keyword\Http\Requests;

use Addweb\Base\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;


class StoreKeywordRequest extends BaseRequest
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
            'client_domain_id' => [
                'required',
                'integer',
                Rule::exists('client_domains', 'id')->whereNull('deleted_at'),
            ],
            'keyword' => 'required|string|max:255',
            'status' => ['nullable', Rule::in([1, 2])],
            'processed_at' => 'nullable|date',
        ];
    }
}

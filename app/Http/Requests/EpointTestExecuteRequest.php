<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpointTestExecuteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'api' => ['required', 'string'],
            'custom_public_key' => ['nullable', 'string', 'max:500'],
            'custom_private_key' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'api.required' => 'API seçilməlidir',
        ];
    }
}

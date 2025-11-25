<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRegistrationFormRequest extends FormRequest
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
            'language' => ['required', 'string', 'in:az,en,ru'],
            'refund' => ['nullable', 'in:0,1'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'language.required' => 'Dil seçilməlidir',
            'language.in' => 'Dil az, en və ya ru olmalıdır',
        ];
    }
}

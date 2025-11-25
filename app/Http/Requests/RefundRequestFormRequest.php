<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundRequestFormRequest extends FormRequest
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
            'card_id' => ['required', 'string', 'max:255'],
            'order_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'currency' => ['required', 'string', 'in:AZN'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'card_id.required' => 'Kart ID-si daxil edilməlidir',
            'order_id.required' => 'Sifariş nömrəsi daxil edilməlidir',
            'amount.required' => 'Məbləğ daxil edilməlidir',
        ];
    }
}

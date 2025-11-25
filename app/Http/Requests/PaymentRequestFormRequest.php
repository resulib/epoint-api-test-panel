<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequestFormRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'currency' => ['required', 'string', 'in:AZN'],
            'language' => ['required', 'string', 'in:az,en,ru'],
            'order_id' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_installment' => ['nullable', 'in:0,1'],
            'success_redirect_url' => ['nullable', 'url', 'max:500'],
            'error_redirect_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Məbləğ daxil edilməlidir',
            'amount.numeric' => 'Məbləğ rəqəm olmalıdır',
            'amount.min' => 'Məbləğ ən az 0.01 olmalıdır',
            'currency.required' => 'Valyuta seçilməlidir',
            'currency.in' => 'Yalnız AZN valyutası dəstəklənir',
            'language.required' => 'Dil seçilməlidir',
            'language.in' => 'Dil az, en və ya ru olmalıdır',
            'order_id.required' => 'Sifariş nömrəsi daxil edilməlidir',
        ];
    }
}

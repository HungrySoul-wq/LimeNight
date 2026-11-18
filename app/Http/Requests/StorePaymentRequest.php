<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:100',
                'max:50000',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:card,cash,online',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Укажи сумму пополнения.',
            'amount.numeric' => 'Сумма должна быть числом.',
            'amount.min' => 'Минимальная сумма пополнения — 100 ₸.',
            'amount.max' => 'Максимальная сумма пополнения — 50 000 ₸.',

            'payment_method.required' => 'Выбери способ оплаты.',
            'payment_method.in' => 'Выбран неподдерживаемый способ оплаты.',
        ];
    }
}

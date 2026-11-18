<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'computer_id' => [
                'required_without:room_id',
                'integer',
                'exists:computers,id',
                function ($attribute, $value, $fail) {
                    if ($this->filled('room_id') && $this->filled('computer_id')) {
                        $fail('Нельзя одновременно выбрать компьютер и комнату.');
                    }
                },
            ],

            'room_id' => [
                'required_without:computer_id',
                'integer',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    if ($this->filled('computer_id') && $this->filled('room_id')) {
                        $fail('Нельзя одновременно выбрать комнату и компьютер.');
                    }
                },
            ],

            'start_time' => [
                'required',
                'date',
                'after_or_equal:now',
            ],

            'package' => [
                'required',
                'in:1_hour,3_hours,5_hours,night',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'computer_id.required_without' => 'Выбери компьютер для бронирования.',
            'room_id.required_without' => 'Выбери комнату для бронирования.',
            'start_time.required' => 'Укажи время начала бронирования.',
            'start_time.after_or_equal' => 'Нельзя бронировать прошедшее время.',
            'package.required' => 'Выбери тариф.',
            'package.in' => 'Выбран неизвестный тариф.',
        ];
    }
}

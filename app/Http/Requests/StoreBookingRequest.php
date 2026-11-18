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
            'computer_id' => ['required', 'integer', 'exists:computers,id'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'package' => ['required', 'in:1_hour,3_hours,5_hours,night'],
        ];
    }
}

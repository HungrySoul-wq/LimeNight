<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComputerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Доступ разрешен (роль admin проверяется через middleware)
    }

    public function rules(): array
    {
        return [
            'zone_id' => 'required|exists:zones,id',
            'pc_number' => 'required|string|max:50|unique:computers,pc_number',
            'ip_address' => 'nullable|ip',
            'status' => 'sometimes|in:available,occupied,maintenance',
            'specs' => 'nullable|string',
        ];
    }
}
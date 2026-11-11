<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'computer_id',
        'start_time',
        'end_time',
        'total_price',
        'status', // 'pending', 'active', 'completed', 'cancelled'
    ];

    // Бронирование создано конкретным пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Бронирование оформлено на конкретный ПК
    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }
}
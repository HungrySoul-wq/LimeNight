<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    // Поля, которые разрешено заполнять через create() и update().
    protected $fillable = [
        'zone_id',
        'name',
        'type',
        'capacity',
        'is_active',
    ];

    // Преобразование типов данных.
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // Комната принадлежит одной зоне.
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // В комнате находятся компьютеры.
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }

    // В комнате могут быть бронирования целиком.
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

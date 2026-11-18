<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Computer extends Model
{
    use HasFactory;

    // Данные компьютера, которые можно сохранять.
    protected $fillable = [
        'zone_id',
        'room_id',
        'pc_number',
        'ip_address',
        'status',
        'specs',
    ];

    protected function casts(): array
    {
        return [
            'pc_number' => 'integer',
            'specs' => 'array',
        ];
    }

    // Компьютер относится к определённой зоне.
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // Компьютер находится в определённой комнате.
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // История бронирований компьютера.
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Игры, установленные на компьютере.
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)->withTimestamps();
    }
}

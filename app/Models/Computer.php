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

    protected $fillable = [
        'zone_id',
        'pc_number',
        'ip_address',
        'status', // 'available', 'occupied', 'maintenance'
        'specs',
    ];

    // Компьютер принадлежит определенной зоне
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // У ПК может быть много бронирований
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // На ПК установлено много игр (связь "многие-ко-многим")
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }
}
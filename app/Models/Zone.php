<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // 'Standard', 'VIP', 'BootCamp'
        'hourly_rate',
        'description',
    ];

    // В игровой зоне находится несколько компьютеров
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }
}
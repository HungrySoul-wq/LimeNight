<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hourly_rate',
        'price_1_hour',
        'price_3_hours',
        'price_5_hours',
        'price_night',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'price_1_hour' => 'decimal:2',
            'price_3_hours' => 'decimal:2',
            'price_5_hours' => 'decimal:2',
            'price_night' => 'decimal:2',
        ];
    }

    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class);
    }
}
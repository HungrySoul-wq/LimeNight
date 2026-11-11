<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'genre',
        'min_age_rating',
        'icon_url',
    ];

    // Игра установлена на многих ПК (связь "многие-ко-многим")
    public function computers(): BelongsToMany
    {
        return $this->belongsToMany(Computer::class);
    }
}
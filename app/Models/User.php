<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'client'
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Пользователь имеет один аккаунт/баланс
    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    // Пользователь имеет много бронирований
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Пользователь совершает много платежей
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
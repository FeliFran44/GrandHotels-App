<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'hotel_id',
        'permisos',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permisos' => 'array',
    ];

    public function comunicadosLeidos()
    {
        return $this->belongsToMany(Comunicado::class, 'comunicado_user')->withTimestamps();
    }

    /**
     * Define la relación con el modelo Hotel.
     * Un usuario (Gerente) pertenece a un solo hotel.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}

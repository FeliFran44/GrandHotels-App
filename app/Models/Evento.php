<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    /**
     * La lista de "permisos" que le dice a Laravel qué campos
     * se pueden guardar desde un formulario.
     */
    protected $fillable = [
        'hotel_id', 
        'user_id', 
        'titulo', 
        'tipo', 
        'fecha_inicio', 
        'fecha_fin',
        'capacidad_esperada',
        'capacidad_maxima',
        'necesidades_seguridad'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capacitacion extends Model
{
    use HasFactory;

    protected $table = 'capacitaciones';

    protected $fillable = [
        'hotel_id',
        'user_id',
        'titulo',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'duracion_aproximada',
        'instructor',
        'participantes',
        'resultados',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Una capacitación puede tener muchos archivos adjuntos.
     */
    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}

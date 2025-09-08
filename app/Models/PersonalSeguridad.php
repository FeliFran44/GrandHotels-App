<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalSeguridad extends Model
{
    use HasFactory;
    protected $table = 'personal_seguridad';

    protected $fillable = [
        'hotel_id',
        'nombre',
        'apellido',
        'puesto',
        'turno',
        'hora_entrada',
        'hora_salida',
        'dias_libres', // <-- Añadido
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // Un empleado puede tener muchos períodos de vacaciones
    public function vacaciones()
    {
        return $this->hasMany(Vacacion::class, 'personal_seguridad_id');
    }
}
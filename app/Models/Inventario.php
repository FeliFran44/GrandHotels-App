<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $table = 'inventario';
    protected $fillable = [
        'hotel_id', 
        'nombre', 
        'marca_modelo', // <-- Añadido
        'ubicacion_exacta', // <-- Añadido
        'fecha_compra', // <-- Añadido
        'estado', 
        'ultima_fecha_mantenimiento', 
        'proxima_fecha_mantenimiento'
    ];
    protected $casts = [
        'ultima_fecha_mantenimiento' => 'date', 
        'proxima_fecha_mantenimiento' => 'date',
        'fecha_compra' => 'date', // <-- Añadido
    ];
    public function hotel() { return $this->belongsTo(Hotel::class); }
}
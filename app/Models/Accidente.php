<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accidente extends Model
{
    use HasFactory;
    protected $table = 'accidentes';
    protected $fillable = [
        'hotel_id', 
        'user_id', 
        'tipo', 
        'fecha_evento', 
        'descripcion', 
        'involucrados', 
        'acciones_tomadas'
    ];
    protected $casts = [
        'fecha_evento' => 'datetime',
    ];
    public function hotel() { return $this->belongsTo(Hotel::class); }
    public function user() { return $this->belongsTo(User::class); }

    /**
     * Un accidente puede tener muchos archivos adjuntos.
     */
    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}
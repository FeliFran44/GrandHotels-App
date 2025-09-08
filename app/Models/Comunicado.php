<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    use HasFactory;
    protected $table = 'comunicados';
    protected $fillable = ['hotel_id', 'user_id', 'prioridad', 'descripcion'];
    
    public function hotel() { return $this->belongsTo(Hotel::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function respuestas() { return $this->hasMany(Respuesta::class)->latest(); }
    public function archivos() { return $this->morphMany(Archivo::class, 'archivable'); }

    // ===== AÑADIR ESTA NUEVA RELACIÓN =====
    // Define qué usuarios han leído este comunicado.
    public function leidoPor()
    {
        return $this->belongsToMany(User::class, 'comunicado_user')->withTimestamps();
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;
    protected $table = 'respuestas';
    protected $fillable = ['comunicado_id', 'user_id', 'cuerpo'];
    
    public function comunicado() { return $this->belongsTo(Comunicado::class); }
    public function user() { return $this->belongsTo(User::class); }

    /**
     * Una respuesta puede tener muchos archivos.
     */
    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}
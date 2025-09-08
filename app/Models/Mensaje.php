<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = ['conversacion_id', 'user_id', 'cuerpo', 'leido_a'];
    protected $casts = [
        'leido_a' => 'datetime',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un mensaje puede tener muchos archivos adjuntos.
     */
    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}
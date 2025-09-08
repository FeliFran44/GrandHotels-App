<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasFactory;
    protected $table = 'conversaciones';
    protected $fillable = ['participante_uno_id', 'participante_dos_id'];

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    public function participanteUno()
    {
        return $this->belongsTo(User::class, 'participante_uno_id');
    }

    public function participanteDos()
    {
        return $this->belongsTo(User::class, 'participante_dos_id');
    }
}
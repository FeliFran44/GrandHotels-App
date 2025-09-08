<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $table = 'hoteles';
    protected $fillable = ['nombre', 'ubicacion'];

    // --- AÑADIR ESTA FUNCIÓN ---
    public function personal()
    {
        return $this->hasMany(PersonalSeguridad::class);
    }
}
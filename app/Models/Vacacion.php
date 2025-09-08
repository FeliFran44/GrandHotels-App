<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    use HasFactory;
    protected $table = 'vacaciones';
    protected $fillable = ['personal_seguridad_id', 'fecha_inicio', 'fecha_fin'];
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function personal()
    {
        return $this->belongsTo(PersonalSeguridad::class, 'personal_seguridad_id');
    }
}
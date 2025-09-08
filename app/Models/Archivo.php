<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivos';

    protected $fillable = [
        'nombre_original',
        'path',
    ];

    /**
     * Define la relación polimórfica.
     * Un archivo puede pertenecer a un Comunicado, una Respuesta, etc.
     */
    public function archivable()
    {
        return $this->morphTo();
    }
}
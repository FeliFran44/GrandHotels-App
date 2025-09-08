<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archivo;
use Illuminate\Support\Facades\Auth;

class ArchivoGeneralController extends Controller
{
    /**
     * Muestra la página del Archivo General con filtros y paginación.
     * Solo accesible para el Coordinador.
     */
    public function index(Request $request)
    {
        // Creamos la consulta base para el modelo Archivo
        // Eager loading de la relación polimórfica 'archivable' y del usuario asociado a ella
        $query = Archivo::with(['archivable.user']);

        // Si está buscando algo específico por nombre de archivo
        if ($request->filled('busqueda')) {
            $query->where('nombre_original', 'like', '%' . $request->busqueda . '%');
        }

        // Si está filtrando por el tipo de adjunto (Comunicado, Accidente, etc.)
        if ($request->filled('tipo')) {
            // Buscamos por el nombre corto del modelo (ej: 'Comunicado')
            $query->where('archivable_type', 'App\\Models\\' . $request->tipo);
        }

        // Ordenamos por fecha de subida (los más nuevos primero) y paginamos los resultados
        $archivos = $query->latest()->paginate(20);

        // Pasamos los datos a la vista
        return view('archivo-general.index', [
            'archivos' => $archivos,
            'filtro_busqueda' => $request->busqueda ?? '',
            'filtro_tipo' => $request->tipo ?? '',
        ]);
    }
}
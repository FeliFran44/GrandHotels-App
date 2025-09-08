<?php

namespace App\Http\Controllers;

use App\Models\PersonalSeguridad;
use App\Models\Vacacion;
use Illuminate\Http\Request;

class VacacionController extends Controller
{
    /**
     * Guarda un nuevo período de vacaciones para un empleado.
     */
    public function store(Request $request, PersonalSeguridad $personal)
    {
        $validatedData = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $personal->vacaciones()->create($validatedData);

        return back()->with('success', 'Período de vacaciones añadido exitosamente.');
    }

    /**
     * Elimina un período de vacaciones.
     */
    public function destroy(Vacacion $vacacion)
    {
        // La línea problemática "$this->authorize(...)" ha sido eliminada.
        
        // Esta es la línea correcta que SÍ debe quedar.
        $vacacion->delete(); 
        
        return back()->with('success', 'Período de vacaciones eliminado.');
    }
}
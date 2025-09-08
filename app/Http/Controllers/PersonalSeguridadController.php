<?php

namespace App\Http\Controllers;

use App\Models\PersonalSeguridad;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PersonalSeguridadController extends Controller
{
    /**
     * Muestra la lista de personal, con filtros para el Coordinador.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PersonalSeguridad::with('hotel')->latest(); // Usamos latest() para ordenar por más nuevo
        $hoteles = Hotel::all();

        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        } 
        elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // --- CAMBIO CLAVE: DE get() A paginate() ---
        $personal = $query->paginate(15); // Traemos 15 resultados por página
        
        return view('personal.index', [
            'personal' => $personal,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id
        ]);
    }

    /**
     * Muestra la vista de supervisión en vivo, solo para el Coordinador.
     */
    public function enVivo()
    {
        if (Auth::user()->rol !== 'Coordinador') {
            abort(403, 'Acción no autorizada.');
        }

        $ahora = Carbon::now(new \DateTimeZone('America/Montevideo'));
        $horaActual = $ahora->format('H:i:s');
        $diaDeLaSemanaActual = $ahora->dayOfWeekIso;
        $diaDeLaSemanaAyer = $ahora->copy()->subDay()->dayOfWeekIso;
        $fechaActual = $ahora->toDateString();

        $personalActivo = PersonalSeguridad::with('hotel')
            ->where(function ($q) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer) {
                // Lógica de turnos (normal y nocturno)
                $q->where(function($sub) use ($horaActual, $diaDeLaSemanaActual){
                    $sub->whereColumn('hora_entrada', '<=', 'hora_salida')
                        ->whereTime('hora_entrada', '<=', $horaActual)
                        ->whereTime('hora_salida', '>=', $horaActual)
                        ->where(function ($q2) use ($diaDeLaSemanaActual) {
                            $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaActual]);
                        });
                });
                $q->orWhere(function($sub) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer){
                    $sub->whereColumn('hora_entrada', '>', 'hora_salida')
                        ->where(function($sub2) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer){
                            $sub2->whereTime('hora_salida', '>=', $horaActual)
                                 ->where(function ($q2) use ($diaDeLaSemanaAyer) {
                                     $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaAyer]);
                                 });
                            $sub2->orWhereTime('hora_entrada', '<=', $horaActual)
                                 ->where(function ($q2) use ($diaDeLaSemanaActual) {
                                     $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaActual]);
                                 });
                        });
                });
            })
            ->whereDoesntHave('vacaciones', function ($q) use ($fechaActual) {
                $q->where('fecha_inicio', '<=', $fechaActual)->where('fecha_fin', '>=', $fechaActual);
            })
            ->get();

        $personalPorHotel = $personalActivo->groupBy('hotel.nombre');
        
        return view('personal.en-vivo', compact('personalPorHotel'));
    }
    
    /**
     * Muestra el formulario para crear un nuevo empleado.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente') {
            $hoteles = Hotel::where('id', $user->hotel_id)->get();
        } else {
            $hoteles = Hotel::all();
        }
        return view('personal.create', compact('hoteles'));
    }

    /**
     * Guarda un nuevo empleado en la base de datos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'puesto' => 'required|string|max:255',
            'turno' => 'required|string|max:255',
            'hotel_id' => 'required|exists:hoteles,id',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i',
            'dias_libres' => 'nullable|array',
        ]);

        if ($user->rol === 'Gerente' && $request->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $validatedData['dias_libres'] = isset($validatedData['dias_libres']) ? implode(',', $validatedData['dias_libres']) : null;

        PersonalSeguridad::create($validatedData);
        return redirect()->route('personal.index')->with('success', 'Empleado guardado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un empleado.
     */
    public function edit(PersonalSeguridad $personal)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $personal->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $personal->load('vacaciones');
        if ($user->rol === 'Gerente') {
            $hoteles = Hotel::where('id', $user->hotel_id)->get();
        } else {
            $hoteles = Hotel::all();
        }
        return view('personal.edit', compact('personal', 'hoteles'));
    }

    /**
     * Actualiza los datos de un empleado en la base de datos.
     */
    public function update(Request $request, PersonalSeguridad $personal)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $personal->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'puesto' => 'required|string|max:255',
            'turno' => 'required|string|max:255',
            'hotel_id' => 'required|exists:hoteles,id',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i',
            'dias_libres' => 'nullable|array',
        ]);

        $validatedData['dias_libres'] = isset($validatedData['dias_libres']) ? implode(',', $validatedData['dias_libres']) : null;

        $personal->update($validatedData);
        return redirect()->route('personal.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Elimina un empleado de la base de datos.
     */
    public function destroy(PersonalSeguridad $personal)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $personal->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $personal->delete();
        return redirect()->route('personal.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}
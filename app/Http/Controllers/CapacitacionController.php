<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapacitacionController extends Controller
{
    /**
     * Muestra la lista de capacitaciones con filtros.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Capacitacion::with(['hotel', 'user'])->latest();
        $hoteles = Hotel::all();

        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        } elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        $capacitaciones = $query->paginate(15);

        return view('capacitaciones.index', [
            'capacitaciones' => $capacitaciones,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva capacitación.
     */
    public function create()
    {
        $user = Auth::user();
        $tipos = [
            'Seguridad Laboral',
            'Primeros Auxilios',
            'Manejo de Crisis',
            'Prevención de Incendios',
            'Evacuación de Emergencia',
            'Atención al Cliente',
            'Manejo de Conflictos',
            'Uso de Equipos',
            'Otro'
        ];
        $hoteles = ($user->rol === 'Gerente') 
            ? Hotel::where('id', $user->hotel_id)->get() 
            : Hotel::all();

        return view('capacitaciones.create', compact('hoteles', 'tipos'));
    }

    /**
     * Guarda una nueva capacitación en la base de datos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'hotel_id' => 'required|exists:hoteles,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|string',
            'fecha_inicio' => 'required|date',
            'duracion_aproximada' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'participantes' => 'nullable|string',
            'resultados' => 'nullable|string',
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400',
        ]);

        if ($user->rol === 'Gerente' && $request->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $validatedData['user_id'] = $user->id;
        $capacitacion = Capacitacion::create($validatedData);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('capacitaciones', 'public');
                $capacitacion->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('capacitaciones.index')->with('success', 'Capacitación registrada exitosamente.');
    }

    /**
     * Muestra los detalles de una capacitación.
     */
    public function show(Capacitacion $capacitacion)
    {
        $user = Auth::user();

        if ($user->rol === 'Gerente' && $capacitacion->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $capacitacion->load(['hotel', 'user', 'archivos']);

        return view('capacitaciones.show', compact('capacitacion'));
    }

    /**
     * Muestra el formulario para editar una capacitación.
     */
    public function edit(Capacitacion $capacitacion)
    {
        $user = Auth::user();

        if ($user->rol === 'Gerente' && $capacitacion->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $tipos = [
            'Seguridad Laboral',
            'Primeros Auxilios',
            'Manejo de Crisis',
            'Prevención de Incendios',
            'Evacuación de Emergencia',
            'Atención al Cliente',
            'Manejo de Conflictos',
            'Uso de Equipos',
            'Otro'
        ];
        $hoteles = ($user->rol === 'Gerente') 
            ? Hotel::where('id', $user->hotel_id)->get() 
            : Hotel::all();

        return view('capacitaciones.edit', compact('capacitacion', 'hoteles', 'tipos'));
    }

    /**
     * Actualiza una capacitación en la base de datos.
     */
    public function update(Request $request, Capacitacion $capacitacion)
    {
        $user = Auth::user();

        if ($user->rol === 'Gerente' && $capacitacion->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'hotel_id' => 'required|exists:hoteles,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|string',
            'fecha_inicio' => 'required|date',
            'duracion_aproximada' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'participantes' => 'nullable|string',
            'resultados' => 'nullable|string',
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400',
        ]);

        $capacitacion->update($validatedData);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('capacitaciones', 'public');
                $capacitacion->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('capacitaciones.index')->with('success', 'Capacitación actualizada exitosamente.');
    }

    /**
     * Elimina una capacitación de la base de datos.
     */
    public function destroy(Capacitacion $capacitacion)
    {
        $user = Auth::user();

        if ($user->rol === 'Gerente' && $capacitacion->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $capacitacion->delete();

        return redirect()->route('capacitaciones.index')->with('success', 'Capacitación eliminada exitosamente.');
    }
}

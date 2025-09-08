<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Evento::with(['hotel', 'user'])->latest();
        $hoteles = Hotel::all();
        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        } elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }
        $eventos = $query->paginate(15);
        return view('planificacion.index', [
            'eventos' => $eventos,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $tipos = ['Social', 'Corporativo', 'Deportivo', 'Concierto', 'Otro'];
        $hoteles = ($user->rol === 'Gerente') ? Hotel::where('id', $user->hotel_id)->get() : Hotel::all();
        return view('planificacion.create', compact('hoteles', 'tipos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'hotel_id' => 'required|exists:hoteles,id',
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'capacidad_esperada' => 'nullable|integer',
            'capacidad_maxima' => 'nullable|integer',
            'necesidades_seguridad' => 'nullable|string',
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400'
        ]);
        if ($user->rol === 'Gerente' && $request->hotel_id != $user->hotel_id) { abort(403); }
        
        $validatedData['user_id'] = $user->id;
        $evento = Evento::create($validatedData);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('eventos', 'public');
                $evento->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }
        return redirect()->route('planificacion.index')->with('success', 'Evento planificado exitosamente.');
    }

    /**
     * Muestra la página de detalle de un evento (LÓGICA REESCRITA).
     */
    public function show($id) // Recibimos el ID en lugar del objeto
    {
        // Buscamos el evento manualmente, incluyendo sus relaciones
        $evento = Evento::with(['archivos', 'hotel', 'user'])->findOrFail($id);

        $user = Auth::user();
        if ($user->rol === 'Gerente' && $evento->hotel_id != $user->hotel_id) {
            abort(403);
        }
        
        return view('planificacion.show', compact('evento'));
    }

    public function edit(Evento $evento){}
    public function update(Request $request, Evento $evento){}
    public function destroy(Evento $evento){}

    public function feed(Request $request)
    {
        $query = Evento::with('hotel');
        $user = Auth::user();
        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        }
        $eventos = $query->get();
        $calendarEvents = $eventos->map(function ($evento) {
            if (!$evento->fecha_inicio || !$evento->fecha_fin || !$evento->hotel) return null;
            return [
                'title' => $evento->titulo,
                'start' => $evento->fecha_inicio->toIso8601String(),
                'end' => $evento->fecha_fin->toIso8601String(),
                'url' => route('planificacion.show', $evento),
                'color' => $evento->hotel->color ?? '#6c757d',
                'extendedProps' => [
                    'hotel' => $evento->hotel->nombre
                ]
            ];
        })->filter();
        return response()->json($calendarEvents);
    }
}
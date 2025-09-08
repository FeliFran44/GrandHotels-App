<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Hotel;
use App\Models\Archivo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComunicadoController extends Controller
{
    /**
     * Muestra la lista de comunicados.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Comunicado::with(['hotel', 'user', 'archivos'])->latest();
        $hoteles = Hotel::all();

        if ($user->rol === 'Gerente') {
            $query->where(function ($q) use ($user) {
                $q->where('hotel_id', $user->hotel_id)
                  ->orWhereNull('hotel_id');
            });
        } 
        elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where('descripcion', 'like', "%{$busqueda}%");
        }
        
        // --- CAMBIO A PAGINATE ---
        $comunicados = $query->paginate(15);
        
        return view('comunicados.index', [
            'comunicados' => $comunicados,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id,
            'filtro_busqueda' => $request->busqueda ?? ''
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo comunicado.
     * Solo accesible para el Coordinador.
     */
    public function create()
    {
        if (Auth::user()->rol !== 'Coordinador') {
            abort(403, 'Acción no autorizada.');
        }

        $hoteles = Hotel::all();
        $prioridades = ['Baja', 'Media', 'Alta', 'Crítica'];
        return view('comunicados.create', compact('hoteles', 'prioridades'));
    }

    /**
     * Guarda el nuevo comunicado y sus archivos adjuntos.
     * Solo accesible para el Coordinador.
     */
    public function store(Request $request)
    {
        if (Auth::user()->rol !== 'Coordinador') {
            abort(403, 'Acción no autorizada.');
        }

        $user = Auth::user();
        $validatedData = $request->validate([
            'hotel_id' => 'nullable|exists:hoteles,id',
            'prioridad' => 'required|string',
            'descripcion' => 'required|string',
            // Límite de tamaño actualizado a 100MB
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400'
        ]);

        $validatedData['user_id'] = $user->id;
        $comunicado = Comunicado::create($validatedData);

        // Lógica para guardar los archivos si existen
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                // Guarda el archivo en 'storage/app/public/comunicados'
                $path = $archivo->store('comunicados', 'public');
                
                // Guarda la referencia en la base de datos
                $comunicado->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('comunicados.index')->with('success', 'Comunicado registrado exitosamente.');
    }

    /**
     * Muestra la página de detalle de un comunicado, sus respuestas y el estado de lectura.
     */
    public function show(Comunicado $comunicado)
    {
        $user = Auth::user();
        
        // Registra que el usuario actual ha leído este comunicado.
        $user->comunicadosLeidos()->syncWithoutDetaching($comunicado->id);

        // Carga todas las relaciones necesarias para la vista
        $comunicado->load(['user', 'hotel', 'archivos', 'respuestas.user', 'leidoPor']);
        
        // Obtenemos todos los gerentes para mostrar su estado de lectura
        $gerentes = User::where('rol', 'Gerente')->with('hotel')->get();
        
        return view('comunicados.show', compact('comunicado', 'gerentes'));
    }
}
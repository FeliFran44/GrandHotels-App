<?php
namespace App\Http\Controllers;

use App\Models\Accidente;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccidenteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Accidente::with(['hotel', 'user'])->latest();
        $hoteles = Hotel::all();
        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        } elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }
        $accidentes = $query->paginate(15);
        return view('accidentes.index', [
            'accidentes' => $accidentes,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $tipos = ['Accidente', 'Incidente'];
        $categorias = config('accidentes.categorias');
        $gravedades = config('accidentes.gravedades');
        $hoteles = ($user->rol === 'Gerente') ? Hotel::where('id', $user->hotel_id)->get() : Hotel::all();
        return view('accidentes.create', compact('hoteles', 'tipos', 'categorias','gravedades'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $hasCategoria = Schema::hasColumn('accidentes', 'categoria');
        $rules = [
            'hotel_id' => 'required|exists:hoteles,id',
            'tipo' => 'required|string',
            'fecha_evento' => 'required|date',
            'descripcion' => 'required|string',
            'involucrados' => 'nullable|string',
            'acciones_tomadas' => 'nullable|string',
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400',
        ];
        if ($hasCategoria) {
            $rules['categoria'] = 'required|string|in:'.implode(',', config('accidentes.categorias'));
        }
        $hasGravedad = Schema::hasColumn('accidentes', 'gravedad');
        if ($hasGravedad) {
            $rules['gravedad'] = 'required|string|in:'.implode(',', config('accidentes.gravedades'));
        }
        $validatedData = $request->validate($rules);
        if ($user->rol === 'Gerente' && $request->hotel_id != $user->hotel_id) {
            abort(403);
        }

        $validatedData['user_id'] = $user->id;
        if (!$hasCategoria) {
            unset($validatedData['categoria']);
        }
        if (!$hasGravedad) {
            unset($validatedData['gravedad']);
        }
        $accidente = Accidente::create($validatedData);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('accidentes', 'public');
                $accidente->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('accidentes.index')->with('success', 'Evento registrado exitosamente.');
    }

    public function show(Accidente $accidente)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $accidente->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $accidente->load('archivos');
        return view('accidentes.show', compact('accidente'));
    }

    public function edit(Accidente $accidente)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $accidente->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $tipos = ['Accidente', 'Incidente'];
        $categorias = config('accidentes.categorias');
        $gravedades = config('accidentes.gravedades');
        $hoteles = ($user->rol === 'Gerente') ? Hotel::where('id', $user->hotel_id)->get() : Hotel::all();
        return view('accidentes.edit', compact('accidente', 'hoteles', 'tipos', 'categorias','gravedades'));
    }

    public function update(Request $request, Accidente $accidente)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $accidente->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $hasCategoria = Schema::hasColumn('accidentes', 'categoria');
        $rules = [
            'hotel_id' => 'required|exists:hoteles,id',
            'tipo' => 'required|string',
            'fecha_evento' => 'required|date',
            'descripcion' => 'required|string',
            'involucrados' => 'nullable|string',
            'acciones_tomadas' => 'nullable|string',
        ];
        if ($hasCategoria) {
            $rules['categoria'] = 'required|string|in:'.implode(',', config('accidentes.categorias'));
        }
        $hasGravedad = Schema::hasColumn('accidentes', 'gravedad');
        if ($hasGravedad) {
            $rules['gravedad'] = 'required|string|in:'.implode(',', config('accidentes.gravedades'));
        }
        $validatedData = $request->validate($rules);
        if (!$hasCategoria) {
            unset($validatedData['categoria']);
        }
        if (!$hasGravedad) {
            unset($validatedData['gravedad']);
        }
        $accidente->update($validatedData);
        return redirect()->route('accidentes.index')->with('success', 'Evento actualizado exitosamente.');
    }

    public function destroy(Accidente $accidente)
    {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $accidente->hotel_id != $user->hotel_id) {
            abort(403);
        }
        $accidente->delete();
        return redirect()->route('accidentes.index')->with('success', 'Evento eliminado exitosamente.');
    }
}

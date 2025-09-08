<?php
namespace App\Http\Controllers;
use App\Models\Inventario;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    public function index(Request $request) // <-- Añadimos Request
    {
        $user = Auth::user();
        $query = Inventario::with('hotel');
        $hoteles = Hotel::all(); // <-- Obtenemos los hoteles para el filtro

        if ($user->rol === 'Gerente') {
            $query->where('hotel_id', $user->hotel_id);
        }
        // Si es Coordinador y está filtrando
        elseif ($user->rol === 'Coordinador' && $request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        $inventario = $query->paginate(15);
        
        return view('inventario.index', [
            'inventario' => $inventario,
            'hoteles' => $hoteles,
            'filtro_hotel_id' => $request->hotel_id
        ]);
    }

    // ... (El resto de las funciones no cambian)
    public function create() {
        $user = Auth::user();
        $estados = ['Operativo', 'En Reparación', 'Fuera de Servicio'];
        $hoteles = ($user->rol === 'Gerente') ? Hotel::where('id', $user->hotel_id)->get() : Hotel::all();
        return view('inventario.create', compact('hoteles', 'estados'));
    }
    public function store(Request $request) {
        $user = Auth::user();
        $validatedData = $request->validate([
            'hotel_id' => 'required|exists:hoteles,id',
            'nombre' => 'required|string|max:255',
            'marca_modelo' => 'nullable|string|max:255',
            'ubicacion_exacta' => 'nullable|string|max:255',
            'fecha_compra' => 'nullable|date',
            'estado' => 'required|string',
            'ultima_fecha_mantenimiento' => 'nullable|date',
            'proxima_fecha_mantenimiento' => 'nullable|date|after_or_equal:ultima_fecha_mantenimiento',
        ]);
        if ($user->rol === 'Gerente' && $request->hotel_id != $user->hotel_id) { abort(403); }
        Inventario::create($validatedData);
        return redirect()->route('inventario.index')->with('success', 'Ítem de inventario registrado.');
    }
    public function edit(Inventario $inventario) {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $inventario->hotel_id != $user->hotel_id) { abort(403); }
        $estados = ['Operativo', 'En Reparación', 'Fuera de Servicio'];
        $hoteles = ($user->rol === 'Gerente') ? Hotel::where('id', $user->hotel_id)->get() : Hotel::all();
        return view('inventario.edit', compact('inventario', 'hoteles', 'estados'));
    }
    public function update(Request $request, Inventario $inventario) {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $inventario->hotel_id != $user->hotel_id) { abort(403); }
        $validatedData = $request->validate([
            'hotel_id' => 'required|exists:hoteles,id',
            'nombre' => 'required|string|max:255',
            'marca_modelo' => 'nullable|string|max:255',
            'ubicacion_exacta' => 'nullable|string|max:255',
            'fecha_compra' => 'nullable|date',
            'estado' => 'required|string',
            'ultima_fecha_mantenimiento' => 'nullable|date',
            'proxima_fecha_mantenimiento' => 'nullable|date|after_or_equal:ultima_fecha_mantenimiento',
        ]);
        $inventario->update($validatedData);
        return redirect()->route('inventario.index')->with('success', 'Ítem de inventario actualizado.');
    }
    public function destroy(Inventario $inventario) {
        $user = Auth::user();
        if ($user->rol === 'Gerente' && $inventario->hotel_id != $user->hotel_id) { abort(403); }
        $inventario->delete();
        return redirect()->route('inventario.index')->with('success', 'Ítem de inventario eliminado.');
    }
}
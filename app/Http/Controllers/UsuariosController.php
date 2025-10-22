<?php
namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    private array $sections = [
        'hoteles','personal','comunicados','inventario','accidentes','planificacion','capacitaciones','reportes','chat','archivo_general','auditoria'
    ];

    public function index()
    {
        $usuarios = User::with('hotel')->orderByRaw("FIELD(rol, 'Coordinador','Gerente') asc")->orderBy('name')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $hoteles = Hotel::orderBy('nombre')->get();
        $sections = $this->sections;
        return view('usuarios.create', compact('hoteles','sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','unique:users,email','regex:/@ghl\.com$/i'],
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:Coordinador,Gerente',
            'hotel_id' => 'nullable|exists:hoteles,id',
            'permisos' => 'array'
        ]);
        if ($validated['rol'] === 'Gerente' && empty($validated['hotel_id'])) {
            return back()->withErrors(['hotel_id' => 'Seleccione un hotel para el Gerente'])->withInput();
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
            'hotel_id' => $validated['hotel_id'] ?? null,
            'permisos' => array_values(array_intersect($request->input('permisos', []), $this->sections)),
        ]);
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado');
    }

    public function edit(User $usuario)
    {
        $hoteles = Hotel::orderBy('nombre')->get();
        $sections = $this->sections;
        return view('usuarios.edit', compact('usuario','hoteles','sections'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','unique:users,email,'.$usuario->id,'regex:/@ghl\.com$/i'],
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|in:Coordinador,Gerente',
            'hotel_id' => 'nullable|exists:hoteles,id',
            'permisos' => 'array'
        ]);
        if ($validated['rol'] === 'Gerente' && empty($validated['hotel_id'])) {
            return back()->withErrors(['hotel_id' => 'Seleccione un hotel para el Gerente'])->withInput();
        }
        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }
        $usuario->rol = $validated['rol'];
        $usuario->hotel_id = $validated['hotel_id'] ?? null;
        $usuario->permisos = array_values(array_intersect($request->input('permisos', []), $this->sections));
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->rol === 'Coordinador') {
            return back()->withErrors(['No se puede eliminar un Coordinador']);
        }
        // Eliminar conversaciones donde participa para evitar violación de FK
        \App\Models\Conversacion::where('participante_uno_id', $usuario->id)
            ->orWhere('participante_dos_id', $usuario->id)
            ->each(function ($conv) {
                // Eliminar archivos asociados a mensajes de la conversación (polimórfico)
                $mensajeIds = $conv->mensajes()->pluck('id');
                if ($mensajeIds->isNotEmpty()) {
                    \App\Models\Archivo::where('archivable_type', \App\Models\Mensaje::class)
                        ->whereIn('archivable_id', $mensajeIds)->delete();
                }
                $conv->delete(); // cascada elimina mensajes por FK
            });

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado');
    }
}

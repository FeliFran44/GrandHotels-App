<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $usuariosParaChatear = collect();
        if ($user->rol === 'Coordinador') {
            $usuariosParaChatear = User::where('rol', 'Gerente')->orderBy('name')->get();
        } else {
            $usuariosParaChatear = User::where('rol', 'Coordinador')->get();
        }
        return view('chat.index', compact('usuariosParaChatear'));
    }

    public function show($participanteId)
    {
        $usuarioActual = Auth::user();
        $otroParticipante = User::findOrFail($participanteId);
        $conversacion = Conversacion::where(function ($query) use ($usuarioActual, $otroParticipante) {
            $query->where('participante_uno_id', $usuarioActual->id)
                  ->where('participante_dos_id', $otroParticipante->id);
        })->orWhere(function ($query) use ($usuarioActual, $otroParticipante) {
            $query->where('participante_uno_id', $otroParticipante->id)
                  ->where('participante_dos_id', $usuarioActual->id);
        })->first();
        if (!$conversacion) {
            $conversacion = Conversacion::create([
                'participante_uno_id' => $usuarioActual->id,
                'participante_dos_id' => $otroParticipante->id,
            ]);
        }
        $conversacion->mensajes()
            ->where('user_id', $otroParticipante->id)
            ->whereNull('leido_a')
            ->update(['leido_a' => Carbon::now()]);

        // Paginamos mensajes (los más recientes primero) y luego invertimos en la vista si hace falta
        $mensajes = $conversacion->mensajes()->with(['user','archivos'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        // Sobrescribimos la relación para que la vista actual (que usa $conversacion->mensajes) no cargue todo
        $conversacion->setRelation('mensajes', $mensajes->getCollection());

        $usuariosParaChatear = ($usuarioActual->rol === 'Coordinador') 
            ? User::where('rol', 'Gerente')->orderBy('name')->get() 
            : User::where('rol', 'Coordinador')->get();
        return view('chat.show', compact('conversacion', 'otroParticipante', 'usuariosParaChatear', 'mensajes'));
    }

    public function storeMessage(Request $request, Conversacion $conversacion)
    {
        $validatedData = $request->validate([
            // Hacemos el cuerpo del mensaje opcional si se envía un archivo
            'cuerpo' => 'nullable|string|required_without:archivos', 
            'archivos' => 'required_without:cuerpo|array',
            'archivos.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400'
        ]);

        $mensaje = $conversacion->mensajes()->create([
            'user_id' => Auth::id(),
            'cuerpo' => $validatedData['cuerpo'] ?? '', // Guardamos el cuerpo si existe
        ]);

        // Lógica para guardar los archivos si existen
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('chats', 'public');
                $mensaje->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return back();
    }
}

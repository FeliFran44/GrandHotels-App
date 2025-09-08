<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunicado;
use Illuminate\Support\Facades\Auth;

class RespuestaController extends Controller
{
    /**
     * Guarda una nueva respuesta y sus archivos adjuntos.
     */
    public function store(Request $request, Comunicado $comunicado)
    {
        $validatedData = $request->validate([
            'cuerpo' => 'required|string',
            // Añadimos la misma validación de archivos que en los comunicados
            'archivos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4,mov,avi|max:102400'
        ]);

        $respuesta = $comunicado->respuestas()->create([
            'user_id' => Auth::id(),
            'cuerpo' => $validatedData['cuerpo'],
        ]);

        // Lógica para guardar los archivos de la respuesta
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                // Usamos una subcarpeta 'respuestas' para mantener el orden
                $path = $archivo->store('respuestas', 'public');
                
                // Guardamos la referencia usando la relación polimórfica
                $respuesta->archivos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return back()->with('success', 'Respuesta enviada.');
    }
}
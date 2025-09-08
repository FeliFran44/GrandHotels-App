<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario;
use App\Models\Conversacion; // <-- Añadido
use Carbon\Carbon;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Este composer se ejecuta cada vez que se carga la vista 'layouts.navigation'
        View::composer('layouts.navigation', function ($view) {
            
            if (Auth::check()) {
                $user = Auth::user();
                
                // --- Lógica para Alertas de Mantenimiento (ya existente) ---
                $mantenimientosPendientes = collect();
                if ($user->rol === 'Coordinador') {
                    $fechaLimite = Carbon::now()->addDays(30);
                    $mantenimientosPendientes = Inventario::with('hotel')
                        ->whereNotNull('proxima_fecha_mantenimiento')
                        ->where('proxima_fecha_mantenimiento', '<=', $fechaLimite)
                        ->orderBy('proxima_fecha_mantenimiento', 'asc')
                        ->get();
                }

                // --- NUEVA LÓGICA PARA NOTIFICACIONES DE CHAT ---
                $mensajesNoLeidosCount = Conversacion::where('participante_uno_id', $user->id)
                    ->orWhere('participante_dos_id', $user->id)
                    ->withCount(['mensajes' => function ($query) use ($user) {
                        $query->where('user_id', '!=', $user->id)->whereNull('leido_a');
                    }])
                    ->get()
                    ->sum('mensajes_count');
                
                // Pasamos AMBAS variables a la vista
                $view->with(compact('mantenimientosPendientes', 'mensajesNoLeidosCount'));
            }
        });
    }
}
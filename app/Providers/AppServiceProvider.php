<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- Añadir esta línea

// Importamos todos los modelos y observadores que vamos a usar
use App\Models\PersonalSeguridad;
use App\Observers\PersonalSeguridadObserver;
use App\Models\Inventario;
use App\Observers\InventarioObserver;
use App\Models\Accidente;
use App\Observers\AccidenteObserver;
use App\Models\Comunicado;
use App\Observers\ComunicadoObserver;
use App\Models\Capacitacion;
use App\Observers\CapacitacionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ===== AÑADIR ESTA LÍNEA =====
        Paginator::useBootstrapFive(); // <-- ¡Esta es la clave!
        // ============================
        
        // Activamos un observador para cada módulo
        PersonalSeguridad::observe(PersonalSeguridadObserver::class);
        Inventario::observe(InventarioObserver::class);
        Accidente::observe(AccidenteObserver::class);
        Comunicado::observe(ComunicadoObserver::class);
        Capacitacion::observe(CapacitacionObserver::class);
    }
}
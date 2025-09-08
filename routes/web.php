<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PersonalSeguridadController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AccidenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RespuestaController;
use App\Http\Controllers\VacacionController;
use App\Http\Controllers\ArchivoGeneralController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ChatController;
use App\Models\Comunicado;
use App\Models\PersonalSeguridad;
use App\Models\Hotel;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $queryNovedades = Comunicado::with(['hotel', 'user'])->latest()->take(5);
        if ($user->rol === 'Gerente') {
            $queryNovedades->where(function ($q) use ($user) {
                $q->where('hotel_id', $user->hotel_id)->orWhereNull('hotel_id');
            });
        }
        $novedades = $queryNovedades->get();
        $ahora = Carbon::now(new \DateTimeZone('America/Montevideo'));
        $horaActual = $ahora->format('H:i:s');
        $diaDeLaSemanaActual = $ahora->dayOfWeekIso;
        $diaDeLaSemanaAyer = $ahora->copy()->subDay()->dayOfWeekIso;
        $fechaActual = $ahora->toDateString();
        $estadoFuerza = [];
        $queryLogic = function ($query) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer, $fechaActual) {
            $query->where(function ($q) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer) {
                $q->where(function($sub) use ($horaActual, $diaDeLaSemanaActual){
                    $sub->whereColumn('hora_entrada', '<=', 'hora_salida')->whereTime('hora_entrada', '<=', $horaActual)->whereTime('hora_salida', '>=', $horaActual)
                        ->where(function ($q2) use ($diaDeLaSemanaActual) {
                            $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaActual]);
                        });
                })->orWhere(function($sub) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer){
                    $sub->whereColumn('hora_entrada', '>', 'hora_salida')
                        ->where(function($sub2) use ($horaActual, $diaDeLaSemanaActual, $diaDeLaSemanaAyer){
                            $sub2->whereTime('hora_salida', '>=', $horaActual)
                                 ->where(function ($q2) use ($diaDeLaSemanaAyer) {
                                     $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaAyer]);
                                 });
                            $sub2->orWhereTime('hora_entrada', '<=', $horaActual)
                                 ->where(function ($q2) use ($diaDeLaSemanaActual) {
                                     $q2->whereNull('dias_libres')->orWhereRaw("FIND_IN_SET(?, dias_libres) = 0", [$diaDeLaSemanaActual]);
                                 });
                        });
                });
            })
            ->whereDoesntHave('vacaciones', function ($q) use ($fechaActual) {
                $q->where('fecha_inicio', '<=', $fechaActual)->where('fecha_fin', '>=', $fechaActual);
            });
        };
        if ($user->rol === 'Coordinador') {
            $estadoFuerza = Hotel::withCount(['personal as personal_activos_count' => $queryLogic])->get();
        } else {
            $personalActivo = PersonalSeguridad::where('hotel_id', $user->hotel_id)->where($queryLogic)->count();
            $hotel = Hotel::find($user->hotel_id);
            if ($hotel) {
                $estadoFuerza = [['nombre' => $hotel->nombre, 'personal_activos_count' => $personalActivo]];
            }
        }
        return view('dashboard', compact('novedades', 'estadoFuerza'));
    })->name('dashboard');

    // --- Grupo de rutas que SOLO el Coordinador puede ver ---
    Route::middleware('is.coordinator')->group(function () {
        Route::resource('hoteles', HotelController::class);
        Route::get('/archivo-general', [ArchivoGeneralController::class, 'index'])->name('archivo-general.index');
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // --- Rutas de Módulos ---
    Route::get('/personal/en-vivo', [PersonalSeguridadController::class, 'enVivo'])->name('personal.en-vivo');
    Route::resource('personal', PersonalSeguridadController::class);
    Route::resource('comunicados', ComunicadoController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('inventario', InventarioController::class);
    Route::resource('accidentes', AccidenteController::class);
    Route::resource('planificacion', EventoController::class);
    Route::get('/planificacion-feed', [EventoController::class, 'feed'])->name('planificacion.feed');

    // --- Rutas para el Chat Privado ---
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{participanteId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversacion}/mensaje', [ChatController::class, 'storeMessage'])->name('chat.storeMessage');
    
    // --- Rutas específicas ---
    Route::post('/comunicados/{comunicado}/respuestas', [RespuestaController::class, 'store'])->name('respuestas.store');
    Route::post('/personal/{personal}/vacaciones', [VacacionController::class, 'store'])->name('vacaciones.store');
    Route::delete('/vacaciones/{vacacion}', [VacacionController::class, 'destroy'])->name('vacaciones.destroy');
    
    // --- Rutas del Perfil de Usuario ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
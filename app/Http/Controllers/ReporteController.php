<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Hotel;
use App\Models\Accidente;
use App\Models\Evento;
use App\Models\Inventario;
use Spatie\Browsershot\Browsershot;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isCoordinator = $user && $user->rol === 'Coordinador';

        if ($isCoordinator) {
            $hoteles = Hotel::orderBy('nombre')->get();
            $hotelId = $request->get('hotel_id');
        } else {
            // Manager scope: force to user's hotel
            $hoteles = Hotel::where('id', $user?->hotel_id)->get();
            $hotelId = $user?->hotel_id;
        }

        $categoria = $request->get('categoria');
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now('America/Montevideo')->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now('America/Montevideo')->endOfDay();

        $reportData = $this->buildReportData($hotelId, $start, $end, $categoria);
        $accidentesAggregates = $this->buildAccidentAggregates($hotelId, $start, $end, $categoria);
        // Construir lista de categorías de manera tolerante si la columna no existe aún
        $accidentCategories = collect(config('accidentes.categorias'));

        return view('reportes.index', [
            'hoteles' => $hoteles,
            'filters' => [
                'hotel_id' => $hotelId,
                'categoria' => $categoria,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'reportData' => $reportData,
            'accidentesAggregates' => $accidentesAggregates,
            'accidentCategories' => $accidentCategories,
            'isCoordinator' => $isCoordinator,
        ]);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $isCoordinator = $user && $user->rol === 'Coordinador';

        // Force scope for non-coordinator
        $hotelId = $isCoordinator ? $request->get('hotel_id') : ($user?->hotel_id);
        $categoria = $request->get('categoria');
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now('America/Montevideo')->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now('America/Montevideo')->endOfDay();

        // Preferimos exportación con Browsershot para replicar exactamente lo que se ve en pantalla
        try {
            if (class_exists(\Spatie\Browsershot\Browsershot::class)) {
                $url = route('reportes.index', [
                    'hotel_id' => $hotelId,
                    'categoria' => $categoria,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'pdf' => 1,
                ], true);

                $sessionCookieName = config('session.cookie');
                $sessionCookieValue = $request->cookie($sessionCookieName);
                $domain = parse_url($url, PHP_URL_HOST) ?: $request->getHost();

                $tempPath = storage_path('app/tmp');
                if (!is_dir($tempPath)) { @mkdir($tempPath, 0777, true); }
                $fileName = 'reporte_' . ($hotelId ?: 'todos') . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf';
                $fullPath = $tempPath . DIRECTORY_SEPARATOR . $fileName;

                Browsershot::url($url)
                    ->showBackground()
                    ->emulateMedia('screen')
                    ->margins(10, 10, 10, 10)
                    ->waitUntilNetworkIdle()
                    ->setDelay(1500)
                    ->deviceScaleFactor(2)
                    ->noSandbox()
                    ->setExtraHttpHeaders(['Accept-Language' => 'es-ES,es;q=0.9'])
                    ->setCookies([
                        [
                            'name' => $sessionCookieName,
                            'value' => (string) $sessionCookieValue,
                            'domain' => $domain,
                        ],
                    ])
                    ->format('A4')
                    ->save($fullPath);

                return response()->download($fullPath)->deleteFileAfterSend(true);
            }
        } catch (\Throwable $e) {
            \Log::warning('Browsershot export failed: '.$e->getMessage());
        }

        // Fallback a DomPDF (sin JS) si Browsershot no está disponible
        $reportData = $this->buildReportData($hotelId, $start, $end, $categoria);
        $accidentesAggregates = $this->buildAccidentAggregates($hotelId, $start, $end, $categoria);

        $pdf = app('dompdf.wrapper')->loadView('reportes.pdf', [
            'reportData' => $reportData,
            'accidentesAggregates' => $accidentesAggregates,
            'generatedAt' => Carbon::now('America/Montevideo'),
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
        ])->setPaper('a4', 'portrait');

        $fileName = 'reporte_' . ($hotelId ?: 'todos') . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }

    private function buildReportData(?string $hotelId, Carbon $start, Carbon $end, ?string $categoria = null): array
    {
        $hasCategoria = Schema::hasColumn('accidentes', 'categoria');
        $categoryColumn = $hasCategoria ? 'categoria' : 'tipo';
        if (!$hasCategoria) { $categoria = null; } // evita filtrar por columna inexistente
        $hotelesQuery = Hotel::query()->orderBy('nombre');
        if (!empty($hotelId)) {
            $hotelesQuery->where('id', $hotelId);
        }
        $hoteles = $hotelesQuery->get();

        $data = [];
        foreach ($hoteles as $hotel) {
            $accidentesQuery = Accidente::with('user')
                ->where('hotel_id', $hotel->id)
                ->whereBetween('fecha_evento', [$start, $end]);
            if (!empty($categoria)) {
                $accidentesQuery->where($categoryColumn, $categoria);
            }
            $accidentes = $accidentesQuery->orderBy('fecha_evento', 'desc')->get();

            $accidentesPorTipoQuery = Accidente::select($categoryColumn.' as categoria', DB::raw('COUNT(*) as total'))
                ->where('hotel_id', $hotel->id)
                ->whereBetween('fecha_evento', [$start, $end]);
            if (!empty($categoria)) {
                $accidentesPorTipoQuery->where($categoryColumn, $categoria);
            }
            $accidentesPorTipo = $accidentesPorTipoQuery->groupBy('categoria')->pluck('total', 'categoria');

            // Conteo de accidentes por día para gráficas de tendencia
            $accidentesPorDia = Accidente::select(DB::raw('DATE(fecha_evento) as fecha'), DB::raw('COUNT(*) as total'))
                ->where('hotel_id', $hotel->id)
                ->whereBetween('fecha_evento', [$start, $end])
                ->when(!empty($categoria), fn($q) => $q->where($categoryColumn, $categoria))
                ->groupBy(DB::raw('DATE(fecha_evento)'))
                ->orderBy('fecha', 'asc')
                ->pluck('total', 'fecha');

            $eventos = Evento::with('user')
                ->where('hotel_id', $hotel->id)
                ->where(function ($q) use ($start, $end) {
                    $q->where('fecha_inicio', '<=', $end)
                      ->where('fecha_fin', '>=', $start);
                })
                ->orderBy('fecha_inicio', 'asc')
                ->get();

            $inventarioTotal = Inventario::where('hotel_id', $hotel->id)->count();
            $inventarioPorEstado = Inventario::select('estado', DB::raw('COUNT(*) as total'))
                ->where('hotel_id', $hotel->id)
                ->groupBy('estado')
                ->pluck('total', 'estado');

            $mantenimientosVencidos = Inventario::where('hotel_id', $hotel->id)
                ->whereNotNull('proxima_fecha_mantenimiento')
                ->where('proxima_fecha_mantenimiento', '<', $end->toDateString())
                ->orderBy('proxima_fecha_mantenimiento')
                ->get();

            $mantenimientosProximos = Inventario::where('hotel_id', $hotel->id)
                ->whereNotNull('proxima_fecha_mantenimiento')
                ->whereBetween('proxima_fecha_mantenimiento', [$start->toDateString(), $end->toDateString()])
                ->orderBy('proxima_fecha_mantenimiento')
                ->get();

            $mantenimientosRealizados = Inventario::where('hotel_id', $hotel->id)
                ->whereNotNull('ultima_fecha_mantenimiento')
                ->whereBetween('ultima_fecha_mantenimiento', [$start->toDateString(), $end->toDateString()])
                ->orderBy('ultima_fecha_mantenimiento', 'desc')
                ->get();

            $data[] = [
                'hotel' => $hotel,
                'accidentes' => [
                    'items' => $accidentes,
                    'por_tipo' => $accidentesPorTipo,
                    'por_dia' => $accidentesPorDia,
                    'total' => $accidentes->count(),
                ],
                'eventos' => [
                    'items' => $eventos,
                    'total' => $eventos->count(),
                ],
                'inventario' => [
                    'total' => $inventarioTotal,
                    'por_estado' => $inventarioPorEstado,
                    'vencidos' => $mantenimientosVencidos,
                    'proximos' => $mantenimientosProximos,
                    'realizados' => $mantenimientosRealizados,
                ],
                'periodo' => [
                    'inicio' => $start->copy(),
                    'fin' => $end->copy(),
                ],
            ];
        }

        return $data;
    }

    private function buildAccidentAggregates(?string $hotelId, Carbon $start, Carbon $end, ?string $categoria = null): array
    {
        $hasCategoria = Schema::hasColumn('accidentes', 'categoria');
        $categoryColumn = $hasCategoria ? 'categoria' : 'tipo';
        if (!$hasCategoria) { $categoria = null; }
        $hotelesQuery = Hotel::query()->orderBy('nombre');
        if (!empty($hotelId)) {
            $hotelesQuery->where('id', $hotelId);
        }
        $hoteles = $hotelesQuery->get(['id', 'nombre', 'color']);
        if ($hoteles->isEmpty()) {
            return [
                'hoteles' => [],
                'labels' => [],
                'series' => [],
                'totalesPorHotel' => [],
                'porTipo' => [],
                'totalAccidentes' => 0,
            ];
        }

        // Totales por hotel
        $totalesQuery = Accidente::select('hotel_id', DB::raw('COUNT(*) as total'))
            ->whereIn('hotel_id', $hoteles->pluck('id'))
            ->whereBetween('fecha_evento', [$start, $end]);
        if (!empty($categoria)) {
            $totalesQuery->where($categoryColumn, $categoria);
        }
        $totales = $totalesQuery->groupBy('hotel_id')->pluck('total', 'hotel_id');

        $totalesPorHotel = [];
        foreach ($hoteles as $h) {
            $totalesPorHotel[$h->nombre] = (int) ($totales[$h->id] ?? 0);
        }

        // Por tipo (global dentro del alcance de hoteles)
        $porTipoQuery = Accidente::select($categoryColumn.' as categoria', DB::raw('COUNT(*) as total'))
            ->whereIn('hotel_id', $hoteles->pluck('id'))
            ->whereBetween('fecha_evento', [$start, $end]);
        if (!empty($categoria)) {
            $porTipoQuery->where($categoryColumn, $categoria);
        }
        $porTipo = $porTipoQuery->groupBy('categoria')->pluck('total', 'categoria')->toArray();

        // Por tipo por hotel
        $porTipoPorHotelRows = Accidente::select('hotel_id', $categoryColumn.' as categoria', DB::raw('COUNT(*) as total'))
            ->whereIn('hotel_id', $hoteles->pluck('id'))
            ->whereBetween('fecha_evento', [$start, $end])
            ->when(!empty($categoria), fn($q) => $q->where($categoryColumn, $categoria))
            ->groupBy('hotel_id', 'categoria')
            ->get();
        $porTipoPorHotel = [];
        foreach ($hoteles as $h) { $porTipoPorHotel[$h->nombre] = []; }
        foreach ($porTipoPorHotelRows as $r) {
            $hotelNombre = optional($hoteles->firstWhere('id', $r->hotel_id))->nombre ?? (string)$r->hotel_id;
            $porTipoPorHotel[$hotelNombre][$r->categoria] = (int)$r->total;
        }

        // Por día y por hotel
        $rows = Accidente::select(DB::raw('DATE(fecha_evento) as fecha'), 'hotel_id', DB::raw('COUNT(*) as total'))
            ->whereIn('hotel_id', $hoteles->pluck('id'))
            ->whereBetween('fecha_evento', [$start, $end])
            ->when(!empty($categoria), fn($q) => $q->where($categoryColumn, $categoria))
            ->groupBy(DB::raw('DATE(fecha_evento)'), 'hotel_id')
            ->orderBy(DB::raw('DATE(fecha_evento)'))
            ->get();

        // Construir eje de fechas continuo (incluye días sin registros)
        $labels = [];
        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();
        while ($cursor->lte($endDay)) {
            $labels[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $series = [];
        foreach ($hoteles as $h) {
            $counts = array_fill(0, count($labels), 0);
            foreach ($rows as $r) {
                if ((int)$r->hotel_id !== (int)$h->id) continue;
                $idx = array_search($r->fecha, $labels, true);
                if ($idx !== false) {
                    $counts[$idx] = (int)$r->total;
                }
            }
            $series[] = [
                'hotel_id' => $h->id,
                'hotel' => $h->nombre,
                'data' => $counts,
            ];
        }

        $colorsByHotel = [];
        foreach ($hoteles as $h) { if (!empty($h->color)) { $colorsByHotel[$h->nombre] = $h->color; } }

        return [
            'hoteles' => $hoteles->map(fn($h) => ['id' => $h->id, 'nombre' => $h->nombre, 'color' => $h->color])->values()->toArray(),
            'labels' => $labels,
            'series' => $series,
            'totalesPorHotel' => $totalesPorHotel,
            'porTipo' => $porTipo,
            'porTipoPorHotel' => $porTipoPorHotel,
            'totalAccidentes' => array_sum($totalesPorHotel),
            'colorsByHotel' => $colorsByHotel,
        ];
    }

    private function authorizeCoordinator(Request $request): void
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'Coordinador') {
            abort(403, 'Acceso restringido a Coordinadores');
        }
    }
}

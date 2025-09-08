<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Hotel;
use App\Models\Accidente;
use App\Models\Evento;
use App\Models\Inventario;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeCoordinator($request);

        $hoteles = Hotel::orderBy('nombre')->get();

        $hotelId = $request->get('hotel_id');
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now('America/Montevideo')->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now('America/Montevideo')->endOfDay();

        $reportData = $this->buildReportData($hotelId, $start, $end);

        return view('reportes.index', [
            'hoteles' => $hoteles,
            'filters' => [
                'hotel_id' => $hotelId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'reportData' => $reportData,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorizeCoordinator($request);

        $hotelId = $request->get('hotel_id');
        $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now('America/Montevideo')->startOfMonth();
        $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now('America/Montevideo')->endOfDay();

        $reportData = $this->buildReportData($hotelId, $start, $end);

        $pdf = Pdf::loadView('reportes.pdf', [
            'reportData' => $reportData,
            'generatedAt' => Carbon::now('America/Montevideo'),
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
        ])->setPaper('a4', 'portrait');

        $fileName = 'reporte_' . ($hotelId ?: 'todos') . '_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }

    private function buildReportData(?string $hotelId, Carbon $start, Carbon $end): array
    {
        $hotelesQuery = Hotel::query()->orderBy('nombre');
        if (!empty($hotelId)) {
            $hotelesQuery->where('id', $hotelId);
        }
        $hoteles = $hotelesQuery->get();

        $data = [];
        foreach ($hoteles as $hotel) {
            $accidentes = Accidente::with('user')
                ->where('hotel_id', $hotel->id)
                ->whereBetween('fecha_evento', [$start, $end])
                ->orderBy('fecha_evento', 'desc')
                ->get();

            $accidentesPorTipo = Accidente::select('tipo', DB::raw('COUNT(*) as total'))
                ->where('hotel_id', $hotel->id)
                ->whereBetween('fecha_evento', [$start, $end])
                ->groupBy('tipo')
                ->pluck('total', 'tipo');

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

    private function authorizeCoordinator(Request $request): void
    {
        $user = $request->user();
        if (!$user || $user->rol !== 'Coordinador') {
            abort(403, 'Acceso restringido a Coordinadores');
        }
    }
}


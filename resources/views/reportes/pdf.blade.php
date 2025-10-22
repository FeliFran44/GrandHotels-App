<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { color: #555; }
        .section { margin-top: 16px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        .table th { background: #f5f5f5; }
        .kpi { display: inline-block; padding: 6px 8px; border: 1px solid #ccc; border-radius: 6px; margin-right: 8px; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Reporte Operativo</div>
        <div class="subtitle">
            Periodo: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }} • Generado: {{ $generatedAt->format('d/m/Y H:i') }}
        </div>
    </div>

    @php
        $labels = $accidentesAggregates['labels'] ?? [];
        $series = $accidentesAggregates['series'] ?? [];
        $hotelesLabels = array_map(fn($s) => $s['hotel'], $series);
        $totalesPorHotel = $accidentesAggregates['totalesPorHotel'] ?? [];
        $porCategoria = $accidentesAggregates['porTipo'] ?? [];
        $porTipoPorHotel = $accidentesAggregates['porTipoPorHotel'] ?? [];
    @endphp

    @isset($accidentesAggregates)
    <div class="section">
        <h4>Resumen de Accidentes (Global)</h4>
        <div>
            <span class="kpi">Total accidentes: <strong>{{ $accidentesAggregates['totalAccidentes'] }}</strong></span>
            @if(!empty($filters['hotel_id']))
                <span class="kpi">Hotel filtrado: <strong>{{ optional(\App\Models\Hotel::find($filters['hotel_id']))?->nombre ?? 'N/A' }}</strong></span>
            @endif
            @if(!empty($filters['categoria']))
                <span class="kpi">Categoría: <strong>{{ $filters['categoria'] }}</strong></span>
            @endif
        </div>

        <h5 class="section">Totales por hotel</h5>
        <table class="table">
            <thead><tr><th>Hotel</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($totalesPorHotel as $hotelNombre => $total)
                    <tr><td>{{ $hotelNombre }}</td><td>{{ $total }}</td></tr>
                @empty
                    <tr><td colspan="2">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

        <h5 class="section">Accidentes por categoría</h5>
        <table class="table">
            <thead><tr><th>Categoría</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($porCategoria as $cat => $total)
                    <tr><td>{{ $cat }}</td><td>{{ $total }}</td></tr>
                @empty
                    <tr><td colspan="2">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

        <h5 class="section">Top categorías por hotel</h5>
        <table class="table">
            <thead><tr><th>Hotel</th><th>Categorías</th></tr></thead>
            <tbody>
                @forelse($porTipoPorHotel as $hotelNombre => $mapa)
                    @php $top = collect($mapa)->sortDesc()->take(3); @endphp
                    <tr>
                        <td>{{ $hotelNombre }}</td>
                        <td>
                            @if($top->isEmpty())
                                <span class="small">Sin datos</span>
                            @else
                                @foreach($top as $t => $v)
                                    {{ $t }} ({{ $v }})@if(! $loop->last), @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>

        @if(count($labels) && count($series))
        <h5 class="section">Tendencia diaria por hotel</h5>
        <table class="table small">
            <thead>
                <tr>
                    <th>Fecha</th>
                    @foreach($hotelesLabels as $hn)
                        <th>{{ $hn }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($labels as $idx => $fecha)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m') }}</td>
                        @foreach($series as $s)
                            <td style="text-align:center">{{ (int)($s['data'][$idx] ?? 0) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endisset

    {{-- Detalle por hotel: solo Accidentes/Incidentes --}}
    @if(!empty($reportData))
        @foreach($reportData as $hotelData)
            <div class="section" style="page-break-inside: avoid;">
                <h3 style="margin:6px 0;">{{ $hotelData['hotel']->nombre }}</h3>
                <div style="margin:6px 0 10px 0;">
                    <span class="kpi">Accidentes/Incidentes: <strong>{{ $hotelData['accidentes']['total'] }}</strong></span>
                    @if(!empty($filters['categoria']))
                        <span class="kpi">Categoría: <strong>{{ $filters['categoria'] }}</strong></span>
                    @endif
                    <span class="kpi">Período: <strong>{{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</strong></span>
                </div>

                <h4 style="margin:8px 0;">Accidentes/Incidentes</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:100px;">Fecha</th>
                            <th style="width:90px;">Tipo</th>
                            <th style="width:140px;">Categoría</th>
                            <th style="width:90px;">Gravedad</th>
                            <th>Descripción</th>
                            <th style="width:140px;">Reportado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotelData['accidentes']['items'] as $acc)
                            <tr>
                                <td>{{ optional($acc->fecha_evento)->format('d/m/Y H:i') }}</td>
                                <td>{{ $acc->tipo }}</td>
                                <td>{{ $acc->categoria ?? '—' }}</td>
                                <td>{{ $acc->gravedad ?? '—' }}</td>
                                <td>{{ $acc->descripcion }}</td>
                                <td>{{ $acc->user?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Sin registros</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @php
                    $items = $hotelData['accidentes']['items'];
                    $porCat = $items->groupBy(fn($a) => $a->categoria ?? '—')->map->count();
                    $porGrav = $items->groupBy(fn($a) => $a->gravedad ?? '—')->map->count();
                @endphp
                <div style="margin-top:10px;">
                    <table class="table" style="width:49%; display:inline-table; vertical-align:top;">
                        <thead><tr><th colspan="2">Resumen por categoría</th></tr></thead>
                        <tbody>
                            @forelse($porCat as $cat => $total)
                                <tr><td style="width:70%;">{{ $cat }}</td><td style="width:30%; text-align:right;">{{ $total }}</td></tr>
                            @empty
                                <tr><td colspan="2">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <table class="table" style="width:49%; display:inline-table; vertical-align:top; margin-left:1%;">
                        <thead><tr><th colspan="2">Resumen por gravedad</th></tr></thead>
                        <tbody>
                            @php $ordenGrav = ['Alta','Media','Baja','—']; @endphp
                            @foreach($ordenGrav as $g)
                                @if(($porGrav[$g] ?? null) !== null)
                                    <tr><td style="width:70%;">{{ $g }}</td><td style="width:30%; text-align:right;">{{ $porGrav[$g] }}</td></tr>
                                @endif
                            @endforeach
                            @if($porGrav->isEmpty())
                                <tr><td colspan="2">Sin datos</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    @endif

</body>
</html>

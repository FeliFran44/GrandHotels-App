@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@if(request('pdf'))
<style>
  [data-hide-on-pdf] { display: none !important; }
  body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .card { box-shadow: none !important; }
</style>
@endif
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reportes</h1>
    <div data-hide-on-pdf>
        <a href="{{ route('reportes.export', ['hotel_id' => $filters['hotel_id'], 'categoria' => $filters['categoria'] ?? null, 'start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
        </a>
    </div>
    </div>

<div class="card mb-4">
    @if(($isCoordinator ?? false))
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Hotel</label>
                <select name="hotel_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($hoteles as $hotel)
                        <option value="{{ $hotel->id }}" {{ ($filters['hotel_id'] ?? '') == $hotel->id ? 'selected' : '' }}>{{ $hotel->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría (Accidente)</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas</option>
                    @foreach(($accidentCategories ?? []) as $t)
                        <option value="{{ $t }}" {{ ($filters['categoria'] ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-secondary w-100" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="small text-muted">Hotel</div>
                <div class="fw-semibold">{{ optional($hoteles->first())->nombre ?? 'Mi Hotel' }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
    @endif
    <div class="card-footer small text-muted">
        Periodo: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}
    </div>
    </div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="p-3 border rounded bg-light h-100">
            <div class="small text-muted">Total Accidentes</div>
            <div class="display-6">{{ $accidentesAggregates['totalAccidentes'] }}</div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="p-3 border rounded bg-light h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="small text-muted">Hoteles incluidos</div>
                <div>
                    @forelse($accidentesAggregates['hoteles'] as $h)
                        <span class="badge bg-secondary me-1">{{ $h['nombre'] }}</span>
                    @empty
                        <span class="text-muted">Sin hoteles</span>
                    @endforelse
                </div>
            </div>
            <div class="text-muted small">Accidentes agregados por período</div>
        </div>
    </div>
 </div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Estadísticas de Accidentes</strong>
        <small class="text-muted">Distribución por hotel y tendencia diaria</small>
    </div>
    <div class="card-body">
        @php
            $labels = $accidentesAggregates['labels'] ?? [];
            $series = $accidentesAggregates['series'] ?? [];
            $totales = $accidentesAggregates['totalesPorHotel'] ?? [];
            $porTipo = $accidentesAggregates['porTipo'] ?? [];
            $porTipoPorHotel = $accidentesAggregates['porTipoPorHotel'] ?? [];
            $colorsByHotel = $accidentesAggregates['colorsByHotel'] ?? [];
        @endphp
        @if((count($labels) && count($series)) || count($totales))
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Accidentes por día (por hotel)</strong>
                        <small class="text-muted">Barras apiladas</small>
                    </div>
                    <canvas id="chartStacked" height="220"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Total por hotel</strong>
                        <small class="text-muted">Distribución</small>
                    </div>
                    <canvas id="chartTotales" height="220"></canvas>
                </div>
            </div>
        </div>
        <script>
            (function(){
                if (!window.Chart) return;
                const palette = ['#2563eb','#16a34a','#dc2626','#d97706','#7c3aed','#0891b2','#059669','#ea580c','#9333ea','#0ea5e9'];
                const labels = @json($labels);
                const rawSeries = @json($series);
                const totalesMap = @json($totales);
                const colorsByHotel = @json($colorsByHotel);
                const totalesLabels = Object.keys(totalesMap);
                const totales = Object.values(totalesMap);

                const datasets = rawSeries.map((s, i) => ({
                    label: s.hotel,
                    data: s.data,
                    backgroundColor: colorsByHotel[s.hotel] ?? palette[i % palette.length],
                    stack: 'stack1'
                }));
                const ctx1 = document.getElementById('chartStacked')?.getContext('2d');
                if (ctx1 && labels.length && datasets.length) {
                    new Chart(ctx1, {
                        type: 'bar',
                        data: { labels, datasets },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } },
                            scales: {
                                x: { stacked: true },
                                y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                            }
                        }
                    });
                }

                const ctx2 = document.getElementById('chartTotales')?.getContext('2d');
                if (ctx2 && totales.length) {
                    const doughnutColors = totalesLabels.map((name, i) => colorsByHotel[name] ?? palette[i % palette.length]);
                    new Chart(ctx2, {
                        type: 'doughnut',
                        data: {
                            labels: totalesLabels,
                            datasets: [{
                                data: totales,
                                backgroundColor: doughnutColors
                            }]
                        },
                        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            })();
        </script>

        <div class="row g-3 mt-3">
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <strong>Accidentes por categoría</strong>
                    <ul class="mt-2 mb-0">
                        @forelse($porTipo as $tipo => $total)
                            <li>{{ $tipo }}: <strong>{{ $total }}</strong></li>
                        @empty
                            <li>Sin datos</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <strong>Top categorías por hotel</strong>
                    <ul class="mt-2 mb-0">
                        @forelse($porTipoPorHotel as $hotelNombre => $mapa)
                            @php
                                $ordenado = collect($mapa)->sortDesc();
                                $top = $ordenado->take(3);
                            @endphp
                            <li>
                                <strong>{{ $hotelNombre }}</strong> —
                                @if($top->isEmpty())
                                    <span class="text-muted">Sin datos</span>
                                @else
                                    @foreach($top as $t => $v)
                                        {{ $t }} ({{ $v }})@if(! $loop->last), @endif
                                    @endforeach
                                @endif
                            </li>
                        @empty
                            <li>Sin datos</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="border rounded p-3 mt-3">
            <strong>Conclusiones rápidas</strong>
            <ul class="mt-2 mb-0">
                @php $totalesMap = $accidentesAggregates['totalesPorHotel'] ?? []; @endphp
                @forelse($porTipoPorHotel as $hotelNombre => $mapa)
                    @php
                        $totalHotel = max(1, (int)($totalesMap[$hotelNombre] ?? 0));
                        $topCat = collect($mapa)->sortDesc()->take(1);
                    @endphp
                    @if($topCat->isEmpty())
                        <li>{{ $hotelNombre }} — sin accidentes en el período.</li>
                    @else
                        @foreach($topCat as $t => $v)
                            @php $pct = round(($v / $totalHotel) * 100); @endphp
                            <li>{{ $hotelNombre }} — principal categoría: <strong>{{ $t }}</strong> ({{ $v }}/{{ $totalHotel }} = {{ $pct }}%).</li>
                        @endforeach
                    @endif
                @empty
                    <li>Sin datos.</li>
                @endforelse
            </ul>
        </div>
        @else
            <div class="alert alert-light">Sin datos suficientes para este período.</div>
        @endif
    </div>
</div>

@endsection

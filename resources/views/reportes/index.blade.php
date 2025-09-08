@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reportes</h1>
    <div>
        <a href="{{ route('reportes.export', ['hotel_id' => $filters['hotel_id'], 'start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]) }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
        </a>
    </div>
    </div>

<div class="card mb-4">
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
</div>

@foreach($reportData as $hotelData)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $hotelData['hotel']->nombre }}</strong>
        <small class="text-muted">Periodo: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</small>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="small text-muted">Accidentes/Incidentes</div>
                    <div class="display-6">{{ $hotelData['accidentes']['total'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="small text-muted">Eventos (Planificación)</div>
                    <div class="display-6">{{ $hotelData['eventos']['total'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="small text-muted">Mantenimientos Vencidos</div>
                    <div class="display-6">{{ $hotelData['inventario']['vencidos']->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 border rounded bg-light h-100">
                    <div class="small text-muted">Mantenimientos Próximos</div>
                    <div class="display-6">{{ $hotelData['inventario']['proximos']->count() }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="mb-2">Detalle de Accidentes</h6>
                <ul class="list-group">
                    @forelse($hotelData['accidentes']['items'] as $acc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $acc->tipo }}</strong> — {{ $acc->fecha_evento->format('d/m/Y H:i') }}
                                <div class="small text-muted">{{ Str::limit($acc->descripcion, 80) }}</div>
                            </div>
                            <span class="badge bg-secondary">{{ $acc->user?->name }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">Sin registros</li>
                    @endforelse
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="mb-2">Eventos Programados</h6>
                <ul class="list-group">
                    @forelse($hotelData['eventos']['items'] as $ev)
                        <li class="list-group-item">
                            <strong>{{ $ev->titulo }}</strong>
                            <div class="small text-muted">{{ $ev->tipo }} — {{ $ev->fecha_inicio->format('d/m/Y H:i') }} a {{ $ev->fecha_fin->format('d/m/Y H:i') }}</div>
                        </li>
                    @empty
                        <li class="list-group-item">Sin registros</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="mt-4">
            <h6 class="mb-2">Inventario y Mantenimiento</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-2 h-100">
                        <strong>Por estado</strong>
                        <ul class="mb-0 mt-2">
                            @forelse($hotelData['inventario']['por_estado'] as $estado => $total)
                                <li>{{ $estado }}: {{ $total }}</li>
                            @empty
                                <li>Sin datos</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 h-100">
                        <strong>Vencidos</strong>
                        <ul class="mb-0 mt-2">
                            @forelse($hotelData['inventario']['vencidos'] as $item)
                                <li>{{ $item->nombre }} — {{ optional($item->proxima_fecha_mantenimiento)->format('d/m/Y') }}</li>
                            @empty
                                <li>Sin datos</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 h-100">
                        <strong>Próximos</strong>
                        <ul class="mb-0 mt-2">
                            @forelse($hotelData['inventario']['proximos'] as $item)
                                <li>{{ $item->nombre }} — {{ optional($item->proxima_fecha_mantenimiento)->format('d/m/Y') }}</li>
                            @empty
                                <li>Sin datos</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection


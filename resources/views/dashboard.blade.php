@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold">Últimas Novedades Generales</h6></div>
                <div class="card-body">
                    @forelse ($novedades as $novedad)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 text-primary">
                                    @if ($novedad->hotel) {{ $novedad->hotel->nombre }} @else Comunicado Corporativo @endif
                                </h5>
                                <small>{{ $novedad->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $novedad->descripcion }}</p>
                            <small class="text-muted">Reportado por: <strong>{{ $novedad->user->name }}</strong> | Prioridad: {{ $novedad->prioridad }}</small>
                        </div>
                    @empty
                        <div class="text-center"><p>No hay novedades generales para mostrar.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold">Personal en vivo (En Turno Ahora)</h6></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                    @if(isset($estadoFuerza) && count($estadoFuerza) > 0)
                        @foreach($estadoFuerza as $hotel)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $hotel['nombre'] }}
                                <span class="badge bg-success rounded-pill">{{ $hotel['personal_activos_count'] }}</span>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item">No hay datos de personal.</li>
                    @endif
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold">Accesos Rápidos</h6></div>
                <div class="card-body">
                    {{-- ===== INICIO DE LA CORRECCIÓN ===== --}}
                    <a href="{{ route('comunicados.create') }}" class="btn btn-primary w-100 mb-2">Registrar Novedad</a>
                    {{-- ===== FIN DE LA CORRECCIÓN ===== --}}
                    <a href="{{ route('personal.create') }}" class="btn btn-outline-secondary w-100">Añadir Empleado</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bitácora de Comunicados</h1>
        @if(Auth::user()->rol === 'Coordinador')
            <a href="{{ route('comunicados.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Registrar Comunicado
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (Auth::user()->rol === 'Coordinador')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('comunicados.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="hotel_id" class="form-label">Filtrar por Hotel</label>
                        <select name="hotel_id" id="hotel_id" class="form-select">
                            <option value="">Todos los Hoteles</option>
                            @foreach ($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ (isset($filtro_hotel_id) && $filtro_hotel_id == $hotel->id) ? 'selected' : '' }}>
                                    {{ $hotel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="busqueda" class="form-label">Buscar por palabra clave</label>
                        <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $filtro_busqueda ?? '' }}" placeholder="Ej: RCP, Protocolo, Incendio...">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary">Buscar y Filtrar</button>
                        <a href="{{ route('comunicados.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @forelse ($comunicados as $comunicado)
                <div class="border-bottom pb-3 mb-3">
                    <a href="{{ route('comunicados.show', $comunicado) }}" class="text-decoration-none text-dark">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">
                                @if($comunicado->hotel)
                                    <span class="text-muted">{{ $comunicado->hotel->nombre }}</span>
                                @else
                                    Comunicado Corporativo
                                @endif
                            </h5>
                            <small>{{ $comunicado->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $comunicado->descripcion }}</p>
                    </a>
                    @if ($comunicado->archivos->isNotEmpty())
                        <div class="mt-2">
                            <strong>Archivos Adjuntos:</strong>
                            <ul class="list-unstyled mb-1">
                                @foreach($comunicado->archivos as $archivo)
                                    <li>
                                        <a href="{{ Storage::url($archivo->path) }}" target="_blank">
                                            <i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <small class="text-muted">Reportado por: <strong>{{ $comunicado->user->name }}</strong> | Prioridad: 
                        @php
                            $badgeColor = 'secondary';
                            switch($comunicado->prioridad) {
                                case 'Baja': $badgeColor = 'success'; break;
                                case 'Media': $badgeColor = 'warning'; break;
                                case 'Alta': $badgeColor = 'danger'; break;
                                case 'Crítica': $badgeColor = 'dark'; break;
                            }
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}">{{ $comunicado->prioridad }}</span>
                    </small>
                </div>
            @empty
                <div class="text-center"><p>No hay comunicados registrados para la selección actual.</p></div>
            @endforelse

            {{-- --- ENLACES DE PAGINACIÓN --- --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $comunicados->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Capacitaciones</h1>
        <a href="{{ route('capacitaciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nueva Capacitación
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros (solo para Coordinador) -->
    @if(Auth::user()->rol === 'Coordinador')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('capacitaciones.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="hotel_id" class="form-label">Filtrar por Hotel</label>
                    <select name="hotel_id" id="hotel_id" class="form-select">
                        <option value="">Todos los hoteles</option>
                        @foreach($hoteles as $hotel)
                            <option value="{{ $hotel->id }}" {{ $filtro_hotel_id == $hotel->id ? 'selected' : '' }}>
                                {{ $hotel->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('capacitaciones.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Tabla de Capacitaciones -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hotel</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha Inicio</th>
                            <th>Duración</th>
                            <th>Instructor</th>
                            <th>Registrado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capacitaciones as $capacitacion)
                            <tr>
                                <td>{{ $capacitacion->hotel->nombre }}</td>
                                <td>{{ $capacitacion->titulo }}</td>
                                <td>{{ $capacitacion->tipo }}</td>
                                <td>{{ $capacitacion->fecha_inicio->format('d/m/Y') }}</td>
                                <td>{{ $capacitacion->duracion_aproximada ?? 'N/A' }}</td>
                                <td>{{ $capacitacion->instructor ?? 'N/A' }}</td>
                                <td>{{ $capacitacion->user->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('capacitaciones.show', $capacitacion) }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('capacitaciones.edit', $capacitacion) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('capacitaciones.destroy', $capacitacion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta capacitación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay capacitaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $capacitaciones->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supervisión de Personal en Vivo</h1>
        <a href="{{ route('personal.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a Gestión de Personal
        </a>
    </div>

    <div class="row">
        @forelse ($personalPorHotel as $nombreHotel => $personal)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h6 class="m-0 font-weight-bold">{{ $nombreHotel }} - <span class="badge bg-success">{{ $personal->count() }} Activos</span></h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($personal as $empleado)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $empleado->nombre }} {{ $empleado->apellido }}</strong>
                                        <small class="d-block text-muted">{{ $empleado->puesto }} (Turno {{ $empleado->turno }})</small>
                                    </div>
                                    <span class="text-muted small">{{ $empleado->hora_entrada }} - {{ $empleado->hora_salida }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No hay personal activo en este momento en ningún hotel.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
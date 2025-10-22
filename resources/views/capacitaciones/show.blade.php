@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Capacitación</h1>
        <div>
            <a href="{{ route('capacitaciones.edit', $capacitacion) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            <a href="{{ route('capacitaciones.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $capacitacion->titulo }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Hotel:</strong>
                            <p>{{ $capacitacion->hotel->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Tipo:</strong>
                            <p>{{ $capacitacion->tipo }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fecha de Inicio:</strong>
                            <p>{{ $capacitacion->fecha_inicio->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Duración Aproximada:</strong>
                            <p>{{ $capacitacion->duracion_aproximada ?? 'No especificada' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Instructor/Facilitador:</strong>
                            <p>{{ $capacitacion->instructor ?? 'No especificado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Participantes:</strong>
                            <p>{{ $capacitacion->participantes ?? 'No especificado' }}</p>
                        </div>
                    </div>

                    @if($capacitacion->descripcion)
                    <div class="mb-3">
                        <strong>Descripción:</strong>
                        <p class="text-muted">{{ $capacitacion->descripcion }}</p>
                    </div>
                    @endif

                    @if($capacitacion->resultados)
                    <div class="mb-3">
                        <strong>Resultados / Observaciones:</strong>
                        <p class="text-muted">{{ $capacitacion->resultados }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Archivos Adjuntos -->
            @if($capacitacion->archivos->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Archivos Adjuntos ({{ $capacitacion->archivos->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($capacitacion->archivos as $archivo)
                            <a href="{{ asset('storage/' . $archivo->path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-file-earmark-arrow-down me-2"></i>
                                    {{ $archivo->nombre_original }}
                                </span>
                                <i class="bi bi-box-arrow-up-right text-muted"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información Secundaria -->
        <div class="col-lg-4">
            <!-- Información de Registro -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Información de Registro</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Registrado por:</strong><br>
                        {{ $capacitacion->user->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Fecha de registro:</strong><br>
                        {{ $capacitacion->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if($capacitacion->updated_at != $capacitacion->created_at)
                    <p class="mb-0">
                        <strong>Última actualización:</strong><br>
                        {{ $capacitacion->updated_at->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

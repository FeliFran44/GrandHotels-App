@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <a href="{{ route('planificacion.index') }}" class="btn btn-secondary mb-4"><i class="bi bi-arrow-left"></i> Volver a la Planificación</a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h3>{{ $evento->titulo ?? 'Detalle del Evento' }} <span class="badge bg-info">{{ $evento->tipo ?? 'N/A' }}</span></h3>
        </div>
        <div class="card-body row">
            <div class="col-lg-8 border-end">
                <h5>Observaciones / Necesidades de Seguridad</h5>
                <p class="text-muted">{{ $evento->necesidades_seguridad ?? 'No se especificaron necesidades.' }}</p>
                
                @if ($evento->archivos && $evento->archivos->isNotEmpty())
                    <hr><h5>Archivos Adjuntos</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($evento->archivos as $archivo)
                            <li class="list-group-item ps-0"><a href="{{ Storage::url($archivo->path) }}" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-lg-4">
                <h5>Información Clave</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Hotel:</span><strong>{{ $evento->hotel->nombre ?? 'N/A' }}</strong></li>
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Desde:</span><strong>{{ $evento->fecha_inicio ? $evento->fecha_inicio->format('d/m/Y H:i') : 'N/A' }} hs</strong></li>
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Hasta:</span><strong>{{ $evento->fecha_fin ? $evento->fecha_fin->format('d/m/Y H:i') : 'N/A' }} hs</strong></li>
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Asistencia Esperada:</span><strong>{{ $evento->capacidad_esperada ?? 'N/A' }}</strong></li>
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Capacidad Máxima:</span><strong>{{ $evento->capacidad_maxima ?? 'N/A' }}</strong></li>
                    <li class="list-group-item ps-0 d-flex justify-content-between"><span>Registrado por:</span><strong>{{ $evento->user->name ?? 'N/A' }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
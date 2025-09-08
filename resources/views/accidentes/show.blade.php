@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle del Evento</h1>
        <a href="{{ route('accidentes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Registro
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-8 border-end">
                    <h4 class="mb-3"><span class="badge bg-{{ $accidente->tipo == 'Accidente' ? 'danger' : 'warning' }} fs-6 me-2">{{ $accidente->tipo }}</span>Descripción Detallada</h4>
                    <p class="text-muted">{{ $accidente->descripcion }}</p>
                    <hr class="my-4">
                    <h5 class="mb-3">Personas Involucradas</h5>
                    <p class="text-muted">{{ $accidente->involucrados ?? 'No se registraron involucrados.' }}</p>
                    <hr class="my-4">
                    <h5 class="mb-3">Acciones Tomadas</h5>
                    <p class="text-muted">{{ $accidente->acciones_tomadas ?? 'No se registraron acciones.' }}</p>
                    
                    {{-- Sección para mostrar archivos adjuntos --}}
                    @if ($accidente->archivos->isNotEmpty())
                        <hr class="my-4">
                        <h5 class="mb-3">Archivos Adjuntos</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($accidente->archivos as $archivo)
                                <li class="list-group-item ps-0"><a href="{{ Storage::url($archivo->path) }}" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-3">Información del Registro</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item ps-0 d-flex justify-content-between"><span>Registrado por:</span><strong>{{ $accidente->user->name }}</strong></li>
                        <li class="list-group-item ps-0 d-flex justify-content-between"><span>Hotel:</span><strong>{{ $accidente->hotel->nombre }}</strong></li>
                        <li class="list-group-item ps-0 d-flex justify-content-between"><span>Fecha del Evento:</span><strong>{{ $accidente->fecha_evento->format('d/m/Y') }}</strong></li>
                        <li class="list-group-item ps-0 d-flex justify-content-between"><span>Hora del Evento:</span><strong>{{ $accidente->fecha_evento->format('H:i') }} hs</strong></li>
                        <li class="list-group-item ps-0 d-flex justify-content-between"><span>Fecha de Registro:</span><strong>{{ $accidente->created_at->format('d/m/Y H:i') }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
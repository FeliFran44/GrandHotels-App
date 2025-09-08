@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Planificar Nuevo Evento</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('planificacion.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3"><label for="titulo" class="form-label">Título del Evento</label><input type="text" class="form-control" name="titulo" required></div>
                @if(Auth::user()->rol === 'Coordinador')
                    <div class="mb-3"><label for="hotel_id" class="form-label">Hotel</label><select class="form-select" name="hotel_id" required>@foreach($hoteles as $hotel)<option value="{{ $hotel->id }}">{{ $hotel->nombre }}</option>@endforeach</select></div>
                @else
                    <input type="hidden" name="hotel_id" value="{{ Auth::user()->hotel_id }}">
                @endif
                <div class="row">
                    <div class="col-md-4 mb-3"><label for="tipo" class="form-label">Tipo de Evento</label><select class="form-select" name="tipo" required>@foreach($tipos as $tipo)<option value="{{ $tipo }}">{{ $tipo }}</option>@endforeach</select></div>
                    <div class="col-md-4 mb-3"><label for="fecha_inicio" class="form-label">Fecha y Hora de Inicio</label><input type="datetime-local" class="form-control" name="fecha_inicio" required></div>
                    <div class="col-md-4 mb-3"><label for="fecha_fin" class="form-label">Fecha y Hora de Fin</label><input type="datetime-local" class="form-control" name="fecha_fin" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="capacidad_esperada" class="form-label">Capacidad Esperada</label><input type="number" class="form-control" name="capacidad_esperada"></div>
                    <div class="col-md-6 mb-3"><label for="capacidad_maxima" class="form-label">Capacidad Máxima</label><input type="number" class="form-control" name="capacidad_maxima"></div>
                </div>
                <div class="mb-3"><label for="necesidades_seguridad" class="form-label">Necesidades de Seguridad (Notas)</label><textarea class="form-control" name="necesidades_seguridad" rows="4"></textarea></div>
                <div class="mb-3"><label for="archivos" class="form-label">Adjuntar Archivos (Planos, etc.)</label><input class="form-control" type="file" name="archivos[]" multiple></div>
                <button type="submit" class="btn btn-primary">Guardar Evento</button>
                <a href="{{ route('planificacion.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
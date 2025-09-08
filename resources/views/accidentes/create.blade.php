@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Registrar Nuevo Accidente o Incidente</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('accidentes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(Auth::user()->rol === 'Coordinador')
                    <div class="mb-3"><label for="hotel_id" class="form-label">Hotel</label><select class="form-select" name="hotel_id" required>@foreach($hoteles as $hotel)<option value="{{ $hotel->id }}">{{ $hotel->nombre }}</option>@endforeach</select></div>
                @else
                    <input type="hidden" name="hotel_id" value="{{ Auth::user()->hotel_id }}">
                @endif
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="tipo" class="form-label">Tipo de Evento</label><select class="form-select" name="tipo" required>@foreach($tipos as $tipo)<option value="{{ $tipo }}">{{ $tipo }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-3"><label for="fecha_evento" class="form-label">Fecha y Hora del Evento</label><input type="datetime-local" class="form-control" name="fecha_evento" required></div>
                </div>
                <div class="mb-3"><label for="descripcion" class="form-label">Descripción Detallada</label><textarea class="form-control" name="descripcion" rows="4" required></textarea></div>
                <div class="mb-3"><label for="involucrados" class="form-label">Personas Involucradas (Opcional)</label><textarea class="form-control" name="involucrados" rows="2"></textarea></div>
                <div class="mb-3"><label for="acciones_tomadas" class="form-label">Acciones Tomadas (Opcional)</label><textarea class="form-control" name="acciones_tomadas" rows="2"></textarea></div>
                
                {{-- Campo para adjuntar archivos --}}
                <div class="mb-3">
                  <label for="archivos" class="form-label">Adjuntar Archivos (Fotos, Videos, etc.)</label>
                  <input class="form-control" type="file" id="archivos" name="archivos[]" multiple>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Evento</button>
                <a href="{{ route('accidentes.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
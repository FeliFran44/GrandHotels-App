@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Editando Evento #{{ $accidente->id }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('accidentes.update', $accidente) }}" method="POST">
                @csrf
                @method('PUT')
                @if(Auth::user()->rol === 'Coordinador')
                    <div class="mb-3"><label for="hotel_id" class="form-label">Hotel</label><select class="form-select" name="hotel_id" required>@foreach($hoteles as $hotel)<option value="{{ $hotel->id }}" {{ $accidente->hotel_id == $hotel->id ? 'selected' : '' }}>{{ $hotel->nombre }}</option>@endforeach</select></div>
                @else
                    <input type="hidden" name="hotel_id" value="{{ Auth::user()->hotel_id }}">
                @endif
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="tipo" class="form-label">Tipo de Evento</label><select class="form-select" name="tipo" required>@foreach($tipos as $tipo)<option value="{{ $tipo }}" {{ $accidente->tipo == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>@endforeach</select></div>
                    <div class="col-md-6 mb-3"><label for="fecha_evento" class="form-label">Fecha y Hora del Evento</label><input type="datetime-local" class="form-control" name="fecha_evento" value="{{ $accidente->fecha_evento->format('Y-m-d\TH:i') }}" required></div>
                </div>
                <div class="mb-3"><label for="descripcion" class="form-label">Descripción Detallada</label><textarea class="form-control" name="descripcion" rows="4" required>{{ $accidente->descripcion }}</textarea></div>
                <div class="mb-3"><label for="involucrados" class="form-label">Personas Involucradas (Opcional)</label><textarea class="form-control" name="involucrados" rows="2">{{ $accidente->involucrados }}</textarea></div>
                <div class="mb-3"><label for="acciones_tomadas" class="form-label">Acciones Tomadas (Opcional)</label><textarea class="form-control" name="acciones_tomadas" rows="2">{{ $accidente->acciones_tomadas }}</textarea></div>
                <button type="submit" class="btn btn-warning">Actualizar Evento</button>
                <a href="{{ route('accidentes.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
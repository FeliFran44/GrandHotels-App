@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Editando a: {{ $personal->nombre }} {{ $personal->apellido }}</h1>

    {{-- Formulario Principal de Datos del Empleado --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header"><h6 class="m-0 font-weight-bold">Datos del Empleado y Horario</h6></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <form action="{{ route('personal.update', $personal) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- (Campos de nombre, apellido, puesto, etc., como antes) --}}
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" name="nombre" value="{{ old('nombre', $personal->nombre) }}" required></div>
                    <div class="col-md-6 mb-3"><label for="apellido" class="form-label">Apellido</label><input type="text" class="form-control" name="apellido" value="{{ old('apellido', $personal->apellido) }}" required></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label for="puesto" class="form-label">Puesto</label><input type="text" class="form-control" name="puesto" value="{{ old('puesto', $personal->puesto) }}" required></div>
                    <div class="col-md-4 mb-3"><label for="turno" class="form-label">Turno</label><input type="text" class="form-control" name="turno" value="{{ old('turno', $personal->turno) }}" required></div>
                    <div class="col-md-4 mb-3"><label for="hotel_id" class="form-label">Hotel</label><select class="form-select" name="hotel_id" required>@foreach ($hoteles as $hotel)<option value="{{ $hotel->id }}" {{ old('hotel_id', $personal->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->nombre }}</option>@endforeach</select></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="hora_entrada" class="form-label">Hora de Entrada</label><input type="time" class="form-control" name="hora_entrada" value="{{ old('hora_entrada', $personal->hora_entrada ? \Carbon\Carbon::parse($personal->hora_entrada)->format('H:i') : '') }}"></div>
                    <div class="col-md-6 mb-3"><label for="hora_salida" class="form-label">Hora de Salida</label><input type="time" class="form-control" name="hora_salida" value="{{ old('hora_salida', $personal->hora_salida ? \Carbon\Carbon::parse($personal->hora_salida)->format('H:i') : '') }}"></div>
                </div>

                {{-- Días Libres --}}
                <div class="mb-3">
                    <label class="form-label">Días Libres Fijos</label>
                    <div class="d-flex flex-wrap">
                        @php $diasLibres = explode(',', $personal->dias_libres); @endphp
                        @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $i => $dia)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="dias_libres[]" value="{{ $i + 1 }}" id="dia-{{ $i }}" {{ in_array($i + 1, $diasLibres) ? 'checked' : '' }}>
                            <label class="form-check-label" for="dia-{{ $i }}">{{ $dia }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">Actualizar Empleado</button>
                <a href="{{ route('personal.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>

    {{-- Gestor de Vacaciones --}}
    <div class="card shadow-sm">
        <div class="card-header"><h6 class="m-0 font-weight-bold">Gestor de Vacaciones</h6></div>
        <div class="card-body">
            <h5>Añadir Nuevo Período de Vacaciones</h5>
            <form action="{{ route('vacaciones.store', $personal) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5"><label for="fecha_inicio" class="form-label">Fecha de Inicio</label><input type="date" name="fecha_inicio" class="form-control" required></div>
                    <div class="col-md-5"><label for="fecha_fin" class="form-label">Fecha de Fin</label><input type="date" name="fecha_fin" class="form-control" required></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-success w-100">Añadir</button></div>
                </div>
            </form>
            <hr>
            <h5>Períodos Registrados</h5>
            @if($personal->vacaciones->isNotEmpty())
            <ul class="list-group">
                @foreach($personal->vacaciones as $vacacion)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Del {{ $vacacion->fecha_inicio->format('d/m/Y') }} al {{ $vacacion->fecha_fin->format('d/m/Y') }}
                    <form action="{{ route('vacaciones.destroy', $vacacion) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Está seguro?')"><i class="bi bi-trash-fill"></i></button>
                    </form>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted">No hay períodos de vacaciones registrados para este empleado.</p>
            @endif
        </div>
    </div>
</div>
@endsection
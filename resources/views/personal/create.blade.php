@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Añadir Nuevo Empleado</h1>
    <div class="card shadow-sm">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>¡Ups! Hubo algunos problemas con su entrada.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('personal.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required></div>
                    <div class="col-md-6 mb-3"><label for="apellido" class="form-label">Apellido</label><input type="text" class="form-control" name="apellido" value="{{ old('apellido') }}" required></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label for="puesto" class="form-label">Puesto</label><input type="text" class="form-control" name="puesto" value="{{ old('puesto') }}" required></div>
                    <div class="col-md-4 mb-3"><label for="turno" class="form-label">Turno</label><input type="text" class="form-control" name="turno" value="{{ old('turno') }}" required></div>
                    <div class="col-md-4 mb-3">
                        <label for="hotel_id" class="form-label">Hotel Asignado</label>
                        <select class="form-select" name="hotel_id" required>
                            <option value="">Seleccione un hotel...</option>
                            @foreach ($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="hora_entrada" class="form-label">Hora de Entrada</label><input type="time" class="form-control" name="hora_entrada" value="{{ old('hora_entrada') }}"></div>
                    <div class="col-md-6 mb-3"><label for="hora_salida" class="form-label">Hora de Salida</label><input type="time" class="form-control" name="hora_salida" value="{{ old('hora_salida') }}"></div>
                </div>

                {{-- Días Libres --}}
                <div class="mb-3">
                    <label class="form-label">Días Libres Fijos</label>
                    <div class="d-flex flex-wrap">
                        @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $i => $dia)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="dias_libres[]" value="{{ $i + 1 }}" id="dia-{{ $i }}">
                            <label class="form-check-label" for="dia-{{ $i }}">{{ $dia }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                <a href="{{ route('personal.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
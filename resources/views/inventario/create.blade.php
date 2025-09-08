@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Añadir Nuevo Ítem al Inventario</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('inventario.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Tipo de Equipo (Ej: Extintor, Detector de Humo)</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="marca_modelo" class="form-label">Marca / Modelo</label>
                        <input type="text" class="form-control" name="marca_modelo">
                    </div>
                </div>

                @if(Auth::user()->rol === 'Coordinador')
                    <div class="mb-3">
                        <label for="hotel_id" class="form-label">Hotel</label>
                        <select class="form-select" name="hotel_id" required>
                            <option value="">Seleccione un hotel...</option>
                            @foreach($hoteles as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="hotel_id" value="{{ Auth::user()->hotel_id }}">
                @endif
                
                <div class="mb-3">
                    <label for="ubicacion_exacta" class="form-label">Ubicación Exacta</label>
                    <input type="text" class="form-control" name="ubicacion_exacta">
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                        <input type="date" class="form-control" name="fecha_compra">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="ultima_fecha_mantenimiento" class="form-label">Último Mantenimiento</label>
                        <input type="date" class="form-control" name="ultima_fecha_mantenimiento">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="proxima_fecha_mantenimiento" class="form-label">Próximo Mantenimiento</label>
                        <input type="date" class="form-control" name="proxima_fecha_mantenimiento">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Ítem</button>
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
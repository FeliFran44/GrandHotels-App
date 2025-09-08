@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Editando Ítem: {{ $inventario->nombre }}</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('inventario.update', $inventario) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Ítem</label>
                    <input type="text" class="form-control" name="nombre" value="{{ $inventario->nombre }}" required>
                </div>

                @if(Auth::user()->rol === 'Coordinador')
                    <div class="mb-3">
                        <label for="hotel_id" class="form-label">Hotel</label>
                        <select class="form-select" name="hotel_id" required>
                            @foreach($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ $inventario->hotel_id == $hotel->id ? 'selected' : '' }}>{{ $hotel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="hotel_id" value="{{ Auth::user()->hotel_id }}">
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" name="estado" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}" {{ $inventario->estado == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ultima_fecha_mantenimiento" class="form-label">Último Mantenimiento</label>
                        <input type="date" class="form-control" name="ultima_fecha_mantenimiento" value="{{ $inventario->ultima_fecha_mantenimiento ? $inventario->ultima_fecha_mantenimiento->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="proxima_fecha_mantenimiento" class="form-label">Próximo Mantenimiento</label>
                        <input type="date" class="form-control" name="proxima_fecha_mantenimiento" value="{{ $inventario->proxima_fecha_mantenimiento ? $inventario->proxima_fecha_mantenimiento->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">Actualizar Ítem</button>
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
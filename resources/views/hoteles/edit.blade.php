@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editando Hotel: {{ $hotel->nombre }}</h1>
        <a href="{{ route('hoteles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al Listado
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            
            <form action="{{ route('hoteles.update', $hotel) }}" method="POST">
                @csrf <!-- Token de seguridad -->
                @method('PUT') <!-- Le decimos a Laravel que es una petición PUT -->

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Hotel</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $hotel->nombre }}" required>
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ $hotel->ubicacion }}">
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-floppy-fill me-1"></i>
                    Actualizar Hotel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
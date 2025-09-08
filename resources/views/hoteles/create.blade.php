@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cargar Nuevo Hotel</h1>
        <a href="{{ route('hoteles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al Listado
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            
            {{-- Bloque para mostrar el mensaje de éxito --}}
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('hoteles.store') }}" method="POST">
                @csrf <!-- Directiva de seguridad OBLIGATORIA en Laravel -->

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Hotel</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy-fill me-1"></i>
                    Guardar Hotel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Listado de Hoteles</h1>
        <a href="{{ route('hoteles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Cargar Nuevo Hotel
        </a>
    </div>

    {{-- Bloque para mostrar mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Ubicación</th>
                            <th scope="col">Fecha de Creación</th>
                            <th scope="col">Acciones</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hoteles as $hotel)
                            <tr>
                                <th scope="row">{{ $hotel->id }}</th>
                                <td>{{ $hotel->nombre }}</td>
                                <td>{{ $hotel->ubicacion }}</td>
                                <td>{{ $hotel->created_at->format('d/m/Y H:i') }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('hoteles.edit', $hotel) }}" class="btn btn-sm btn-warning me-2">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    
                                    {{-- FORMULARIO PARA ELIMINAR --}}
                                    <form action="{{ route('hoteles.destroy', $hotel) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este hotel? Esta acción no se puede deshacer.')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay hoteles registrados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

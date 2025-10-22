@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Inventario</h1>
        <a href="{{ route('inventario.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Añadir Ítem</a>
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    @if (Auth::user()->rol === 'Coordinador')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inventario.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="hotel_id" class="form-label">Filtrar por Hotel</label>
                        <select name="hotel_id" id="hotel_id" class="form-select">
                            <option value="">Todos los Hoteles</option>
                            @foreach ($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ (isset($filtro_hotel_id) && $filtro_hotel_id == $hotel->id) ? 'selected' : '' }}>
                                    {{ $hotel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary">Filtrar</button>
                        <a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light"><tr><th>Nombre</th><th>Estado</th><th>Hotel</th><th>Próximo Mantenimiento</th><th>Acciones</th></tr></thead>
                    <tbody>
                        @forelse ($inventario as $item)
                            <tr style="cursor:pointer;" onclick="window.location='{{ route('inventario.show', $item) }}'">
                                <td><a href="{{ route('inventario.show', $item) }}" class="text-decoration-none">{{ $item->nombre }}</a></td>
                                <td>{{ $item->estado }}</td>
                                <td>{{ $item->hotel->nombre }}</td>
                                <td>{{ $item->proxima_fecha_mantenimiento ? $item->proxima_fecha_mantenimiento->format('d/m/Y') : 'N/A' }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('inventario.edit', $item) }}" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('inventario.destroy', $item) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')"><i class="bi bi-trash-fill"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No hay ítems registrados para la selección actual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- --- ENLACES DE PAGINACIÓN --- --}}
            <div class="d-flex justify-content-center">
                {{ $inventario->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

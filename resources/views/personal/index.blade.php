@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Personal</h1>
        <div>
            @if(Auth::user()->rol === 'Coordinador')
                <a href="{{ route('personal.en-vivo') }}" class="btn btn-info me-2">
                    <i class="bi bi-broadcast me-1"></i>
                    Ver Personal en Vivo
                </a>
            @endif
            @if(Auth::user()->rol === 'Gerente')
                <a href="{{ route('personal.en-vivo-mi-hotel') }}" class="btn btn-info me-2">
                    <i class="bi bi-broadcast me-1"></i>
                    Ver Personal en Vivo
                </a>
            @endif
            <a href="{{ route('personal.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Añadir Empleado
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Formulario de Filtro (Solo para Coordinador) --}}
    @if (Auth::user()->rol === 'Coordinador')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('personal.index') }}">
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
                        <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Puesto</th>
                            <th>Turno</th>
                            <th>Horario</th>
                            <th>Hotel</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($personal as $empleado)
                            <tr>
                                <td>{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                                <td>{{ $empleado->puesto }}</td>
                                <td>{{ $empleado->turno }}</td>
                                <td>{{ $empleado->hora_entrada ? \Carbon\Carbon::parse($empleado->hora_entrada)->format('H:i') : 'N/A' }} - {{ $empleado->hora_salida ? \Carbon\Carbon::parse($empleado->hora_salida)->format('H:i') : 'N/A' }}</td>
                                <td>{{ $empleado->hotel->nombre }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('personal.edit', $empleado) }}" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('personal.destroy', $empleado) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay personal registrado para la selección actual.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- --- ENLACES DE PAGINACIÓN --- --}}
            <div class="d-flex justify-content-center">
                {{ $personal->appends(request()->query())->links() }}
            </div>
            
        </div>
    </div>
</div>
@endsection

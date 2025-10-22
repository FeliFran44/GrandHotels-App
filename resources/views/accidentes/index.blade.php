@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registro de Accidentes / Incidentes</h1>
        <a href="{{ route('accidentes.create') }}" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i> Registrar Evento</a>
    </div>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    @if (Auth::user()->rol === 'Coordinador')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('accidentes.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="hotel_id" class="form-label">Filtrar por Hotel</label>
                        <select name="hotel_id" id="hotel_id" class="form-select">
                            <option value="">Todos los Hoteles</option>
                            @foreach ($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ (isset($filtro_hotel_id) && $filtro_hotel_id == $hotel->id) ? 'selected' : '' }}>{{ $hotel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary">Filtrar</button>
                        <a href="{{ route('accidentes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Gravedad</th>
                            <th>Descripción</th>
                            <th>Hotel</th>
                            <th>Fecha</th>
                            <th>Registrado Por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accidentes as $evento)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('accidentes.show', $evento) }}';">
                                <td><span class="badge bg-{{ $evento->tipo == 'Accidente' ? 'danger' : 'warning' }}">{{ $evento->tipo }}</span></td>
                                <td><span class="badge bg-secondary">{{ $evento->categoria ?? '—' }}</span></td>
                                <td>
                                    @php $g=$evento->gravedad; $cls=$g==='Alta'?'danger':($g==='Media'?'warning':'success'); @endphp
                                    <span class="badge bg-{{ $cls }}">{{ $g ?? '—' }}</span>
                                </td>
                                <td>{{ Str::limit($evento->descripcion, 50) }}</td>
                                <td>{{ $evento->hotel->nombre }}</td>
                                <td>{{ $evento->fecha_evento->format('d/m/Y H:i') }}</td>
                                <td>{{ $evento->user->name }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('accidentes.edit', $evento) }}" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('accidentes.destroy', $evento) }}" method="POST" onclick="event.stopPropagation();">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No hay eventos registrados para la selección actual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $accidentes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

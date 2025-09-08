@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Archivo General de Documentos</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('archivo-general.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="busqueda" class="form-label">Buscar por nombre de archivo</label>
                        <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $filtro_busqueda ?? '' }}" placeholder="Ej: protocolo_evacuacion.pdf...">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Filtrar por Módulo</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="">Todos los Módulos</option>
                            <option value="Comunicado" {{ (isset($filtro_tipo) && $filtro_tipo == 'Comunicado') ? 'selected' : '' }}>Comunicados</option>
                            <option value="Respuesta" {{ (isset($filtro_tipo) && $filtro_tipo == 'Respuesta') ? 'selected' : '' }}>Respuestas</option>
                            <option value="Accidente" {{ (isset($filtro_tipo) && $filtro_tipo == 'Accidente') ? 'selected' : '' }}>Accidentes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary">Buscar</button>
                        <a href="{{ route('archivo-general.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre del Archivo</th>
                            <th>Módulo de Origen</th>
                            <th>Subido por</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($archivos as $archivo)
                            {{-- Verificamos que el objeto al que pertenece el archivo todavía exista --}}
                            @if($archivo->archivable)
                            <tr>
                                <td><strong>{{ $archivo->nombre_original }}</strong></td>
                                <td>
                                    @php
                                        $contexto = class_basename($archivo->archivable_type);
                                        $ruta = '#';
                                        if ($contexto === 'Comunicado') {
                                            $ruta = route('comunicados.show', $archivo->archivable_id);
                                        } elseif ($contexto === 'Respuesta' && $archivo->archivable->comunicado) {
                                            $ruta = route('comunicados.show', $archivo->archivable->comunicado_id);
                                        } elseif ($contexto === 'Accidente') {
                                            $ruta = route('accidentes.show', $archivo->archivable_id);
                                        }
                                    @endphp
                                    <a href="{{ $ruta }}">
                                        {{ $contexto }} #{{ $archivo->archivable_id }}
                                    </a>
                                </td>
                                <td>{{ $archivo->archivable->user->name ?? 'N/A' }}</td>
                                <td>{{ $archivo->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ Storage::url($archivo->path) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="bi bi-download"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron archivos con los criterios actuales.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            <div class="d-flex justify-content-center">
                {{ $archivos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
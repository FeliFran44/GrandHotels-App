@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Bitácora de Auditoría</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit-log.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-3"><label for="user_id">Usuario</label><select name="user_id" class="form-select"><option value="">Todos</option>@foreach($users as $user)<option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><label for="action">Acción</label><select name="action" class="form-select"><option value="">Todas</option><option value="created" {{ ($filters['action'] ?? '') == 'created' ? 'selected' : '' }}>Creado</option><option value="updated" {{ ($filters['action'] ?? '') == 'updated' ? 'selected' : '' }}>Actualizado</option><option value="deleted" {{ ($filters['action'] ?? '') == 'deleted' ? 'selected' : '' }}>Eliminado</option></select></div>
                    <div class="col-md-3"><label for="fecha_desde">Desde</label><input type="date" name="fecha_desde" class="form-control" value="{{ $filters['fecha_desde'] ?? '' }}"></div>
                    <div class="col-md-3"><label for="fecha_hasta">Hasta</label><input type="date" name="fecha_hasta" class="form-control" value="{{ $filters['fecha_hasta'] ?? '' }}"></div>
                    <div class="col-md-1 d-flex align-items-end"><button type="submit" class="btn btn-secondary w-100">Filtrar</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-sm table-hover">
                <thead class="table-light"><tr><th>Usuario</th><th>Acción</th><th>Módulo Afectado</th><th>Fecha</th><th>Detalles</th></tr></thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                    <tr>
                        <td>{{ $log->user->name ?? 'Sistema' }}</td>
                        <td>
                            @if($log->action == 'created')<span class="badge bg-success">CREADO</span>
                            @elseif($log->action == 'updated')<span class="badge bg-warning text-dark">ACTUALIZADO</span>
                            @else<span class="badge bg-danger">ELIMINADO</span>
                            @endif
                        </td>
                        <td>{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $log->id }}">Ver Cambios</button></td>
                    </tr>

                    {{-- Modal de Detalles --}}
                    <div class="modal fade" id="detailsModal-{{ $log->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $log->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailsModalLabel-{{ $log->id }}">Detalles de la Acción</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Valores Anteriores:</h6>
                                            @if ($log->old_values)
                                                <ul class="list-group list-group-flush">
                                                    @foreach($log->old_values as $key => $value)
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                                                {{ is_array($value) ? json_encode($value) : $value }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>N/A (Fue una creación o no hay datos anteriores)</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Valores Nuevos/Cambiados:</h6>
                                            @if ($log->new_values)
                                                <ul class="list-group list-group-flush">
                                                    @foreach($log->new_values as $key => $value)
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
                                                                {{ is_array($value) ? json_encode($value) : $value }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>N/A (Fue una eliminación o no hay datos nuevos)</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="5" class="text-center">No hay registros con los criterios actuales.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center">{{ $auditLogs->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
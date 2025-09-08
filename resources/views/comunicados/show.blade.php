@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <a href="{{ route('comunicados.index') }}" class="btn btn-secondary mb-4"><i class="bi bi-arrow-left"></i> Volver a la lista</a>
            
            <!-- Tarjeta del Comunicado Principal -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@if($comunicado->hotel) {{ $comunicado->hotel->nombre }} @else Comunicado Corporativo @endif</h5>
                    <small>{{ $comunicado->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-body">
                    <p>{{ $comunicado->descripcion }}</p>
                    @if ($comunicado->archivos->isNotEmpty())
                        <hr><h6>Archivos Adjuntos:</h6>
                        <ul class="list-unstyled">
                            @foreach($comunicado->archivos as $archivo)
                                <li><a href="{{ Storage::url($archivo->path) }}" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="card-footer text-muted d-flex justify-content-between">
                    <span>Reportado por: <strong>{{ $comunicado->user->name }}</strong></span>
                    <span>Prioridad: <span class="badge bg-secondary">{{ $comunicado->prioridad }}</span></span>
                </div>
            </div>

            <!-- Sección de Respuestas -->
            <h4 class="mb-3">Respuestas ({{ $comunicado->respuestas->count() }})</h4>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('respuestas.store', $comunicado) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3"><textarea name="cuerpo" class="form-control" rows="3" placeholder="Escriba su respuesta..." required></textarea></div>
                        <div class="mb-3"><label for="archivos" class="form-label">Adjuntar Archivos (Opcional)</label><input class="form-control" type="file" id="archivos" name="archivos[]" multiple></div>
                        <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
                    </form>
                </div>
            </div>

            @forelse($comunicado->respuestas as $respuesta)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <p>{{ $respuesta->cuerpo }}</p>
                    @if ($respuesta->archivos->isNotEmpty())
                        <div class="mt-2"><strong>Archivos Adjuntos:</strong>
                            <ul class="list-unstyled mb-1">
                                @foreach($respuesta->archivos as $archivo)
                                    <li><a href="{{ Storage::url($archivo->path) }}" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted d-flex justify-content-between">
                    <span><strong>{{ $respuesta->user->name }}</strong></span>
                    <span>{{ $respuesta->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <p>No hay respuestas todavía.</p>
            @endforelse
        </div>

        {{-- ===== INICIO DEL PANEL DE ESTADO DE LECTURA (SOLO PARA COORDINADOR) ===== --}}
        @if(Auth::user()->rol === 'Coordinador')
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Estado de Lectura</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($gerentes as $gerente)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $gerente->name }}</strong>
                                <small class="d-block text-muted">{{ $gerente->hotel->nombre ?? 'N/A' }}</small>
                            </div>
                            @if($comunicado->leidoPor->contains($gerente))
                                <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Visto</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> Pendiente</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        {{-- ===== FIN DEL PANEL DE ESTADO DE LECTURA ===== --}}
    </div>
</div>
@endsection
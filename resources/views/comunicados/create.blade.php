@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Registrar Nuevo Comunicado</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- AÑADIMOS enctype PARA PERMITIR LA SUBIDA DE ARCHIVOS --}}
            <form action="{{ route('comunicados.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Autor del Reporte</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                </div>

                <div class="mb-3">
                    <label for="hotel_id" class="form-label">Hotel (Opcional para comunicados generales)</label>
                    <select class="form-select" name="hotel_id">
                        <option value="">Comunicado General (Para todos los hoteles)</option>
                        @foreach($hoteles as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="prioridad" class="form-label">Prioridad</label>
                    <select class="form-select" name="prioridad" required>
                        @foreach($prioridades as $prioridad)
                            <option value="{{ $prioridad }}">{{ $prioridad }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Comunicado</label>
                    <textarea class="form-control" name="descripcion" rows="4" required></textarea>
                </div>

                {{-- ===== NUEVO CAMPO PARA SUBIR ARCHIVOS ===== --}}
                <div class="mb-3">
                  <label for="archivos" class="form-label">Adjuntar Archivos (Opcional)</label>
                  <input class="form-control" type="file" id="archivos" name="archivos[]" multiple>
                </div>

                <button type="submit" class="btn btn-primary">Registrar</button>
                <a href="{{ route('comunicados.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
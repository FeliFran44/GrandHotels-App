@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registrar Nueva Capacitación</h1>
        <a href="{{ route('capacitaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('capacitaciones.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Hotel -->
                    <div class="col-md-6 mb-3">
                        <label for="hotel_id" class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" id="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror" required>
                            <option value="">Seleccione un hotel</option>
                            @foreach($hoteles as $hotel)
                                <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                    {{ $hotel->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">Tipo de Capacitación <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccione el tipo</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Título -->
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Fecha Inicio -->
                    <div class="col-md-4 mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Duración Aproximada -->
                    <div class="col-md-4 mb-3">
                        <label for="duracion_aproximada" class="form-label">Duración Aproximada</label>
                        <input type="text" name="duracion_aproximada" id="duracion_aproximada" class="form-control @error('duracion_aproximada') is-invalid @enderror" value="{{ old('duracion_aproximada') }}" placeholder="Ej: 4 horas">
                        @error('duracion_aproximada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Instructor -->
                    <div class="col-md-4 mb-3">
                        <label for="instructor" class="form-label">Instructor/Facilitador</label>
                        <input type="text" name="instructor" id="instructor" class="form-control @error('instructor') is-invalid @enderror" value="{{ old('instructor') }}">
                        @error('instructor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Participantes -->
                <div class="mb-3">
                    <label for="participantes" class="form-label">Participantes</label>
                    <textarea name="participantes" id="participantes" rows="2" class="form-control @error('participantes') is-invalid @enderror" placeholder="Ejemplo: Personal de Seguridad">{{ old('participantes') }}</textarea>
                    @error('participantes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Resultados -->
                <div class="mb-3">
                    <label for="resultados" class="form-label">Resultados / Observaciones</label>
                    <textarea name="resultados" id="resultados" rows="3" class="form-control @error('resultados') is-invalid @enderror" placeholder="Observaciones, evaluaciones o resultados post-capacitación">{{ old('resultados') }}</textarea>
                    @error('resultados')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Archivos Adjuntos -->
                <div class="mb-3">
                    <label for="archivos" class="form-label">Archivos Adjuntos</label>
                    <input type="file" name="archivos[]" id="archivos" class="form-control @error('archivos.*') is-invalid @enderror" multiple>
                    <small class="text-muted">Formatos permitidos: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, mp4, mov, avi. Máximo 100MB por archivo.</small>
                    @error('archivos.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('capacitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Capacitación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

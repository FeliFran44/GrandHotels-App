@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Ítem de Inventario</h1>
        <div>
            <a href="{{ route('inventario.edit', $inventario) }}" class="btn btn-warning me-2"><i class="bi bi-pencil"></i> Editar</a>
            <a href="{{ route('inventario.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h5 class="mb-3">Información General</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Nombre / Tipo:</span><strong>{{ $inventario->nombre }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Marca / Modelo:</span><strong>{{ $inventario->marca_modelo ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Hotel:</span><strong>{{ $inventario->hotel?->nombre ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Ubicación Exacta:</span><strong>{{ $inventario->ubicacion_exacta ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Estado:</span><strong>{{ $inventario->estado }}</strong></li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h5 class="mb-3">Fechas</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Fecha de Compra:</span><strong>{{ optional($inventario->fecha_compra)->format('d/m/Y') ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Último Mantenimiento:</span><strong>{{ optional($inventario->ultima_fecha_mantenimiento)->format('d/m/Y') ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Próximo Mantenimiento:</span><strong>{{ optional($inventario->proxima_fecha_mantenimiento)->format('d/m/Y') ?? 'N/A' }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Creado:</span><strong>{{ $inventario->created_at->format('d/m/Y H:i') }}</strong></li>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span>Actualizado:</span><strong>{{ $inventario->updated_at->format('d/m/Y H:i') }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


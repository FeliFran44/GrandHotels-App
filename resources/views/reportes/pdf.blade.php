<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { color: #555; }
        .section { margin-top: 14px; }
        .hotel { margin: 16px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .table th { background: #f5f5f5; }
        .kpi { display: inline-block; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; margin-right: 8px; }
    </style>
    <title>Reporte</title>
    </head>
<body>
    <div class="header">
        <div class="title">Reporte Operativo</div>
        <div class="subtitle">Periodo: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }} · Generado: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    </div>

    @foreach($reportData as $hotelData)
        <div class="hotel">
            <h3>{{ $hotelData['hotel']->nombre }}</h3>
            <div class="kpi">Accidentes/Incidentes: <strong>{{ $hotelData['accidentes']['total'] }}</strong></div>
            <div class="kpi">Eventos: <strong>{{ $hotelData['eventos']['total'] }}</strong></div>
            <div class="kpi">Mant. Vencidos: <strong>{{ $hotelData['inventario']['vencidos']->count() }}</strong></div>
            <div class="kpi">Mant. Próximos: <strong>{{ $hotelData['inventario']['proximos']->count() }}</strong></div>

            <div class="section">
                <h4>Accidentes/Incidentes</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Reportado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotelData['accidentes']['items'] as $acc)
                            <tr>
                                <td>{{ $acc->fecha_evento->format('d/m/Y H:i') }}</td>
                                <td>{{ $acc->tipo }}</td>
                                <td>{{ $acc->descripcion }}</td>
                                <td>{{ $acc->user?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Sin registros</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h4>Eventos (Planificación)</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Título</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotelData['eventos']['items'] as $ev)
                            <tr>
                                <td>{{ $ev->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>{{ $ev->fecha_fin->format('d/m/Y H:i') }}</td>
                                <td>{{ $ev->titulo }}</td>
                                <td>{{ $ev->tipo }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Sin registros</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h4>Inventario y Mantenimiento</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ítem</th>
                            <th>Estado</th>
                            <th>Últ. Mant.</th>
                            <th>Próx. Mant.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $items = $hotelData['inventario']['vencidos']->merge($hotelData['inventario']['proximos'])->unique('id'); @endphp
                        @forelse($items as $it)
                            <tr>
                                <td>{{ $it->nombre }}</td>
                                <td>{{ $it->estado }}</td>
                                <td>{{ optional($it->ultima_fecha_mantenimiento)->format('d/m/Y') }}</td>
                                <td>{{ optional($it->proxima_fecha_mantenimiento)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Sin registros</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <p><strong>Resumen por estado:</strong>
                    @forelse($hotelData['inventario']['por_estado'] as $estado => $total)
                        {{ $estado }} ({{ $total }})@if(!$loop->last), @endif
                    @empty
                        Sin datos
                    @endforelse
                </p>
            </div>
        </div>
        <hr>
    @endforeach
</body>
</html>


@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Planificación de Eventos</h1>
        <a href="{{ route('planificacion.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Planificar Evento</a>
    </div>
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Añadimos las librerías de FullCalendar --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Vista mensual por defecto
            locale: 'es', // Ponemos el calendario en español
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '{{ route('planificacion.feed') }}', // La ruta que creamos para obtener los eventos
            eventDidMount: function(info) {
                // Para mostrar el hotel en el tooltip (opcional, pero queda bien)
                var tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.extendedProps.hotel,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>
@endpush
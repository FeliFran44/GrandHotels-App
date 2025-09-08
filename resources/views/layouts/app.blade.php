<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Grand Hotels Lux Seguridad</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="page-wrapper">
    @include('layouts.navigation')

    <main class="content-wrapper">
        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- ===== INICIO DEL SCRIPT DEL RELOJ ===== --}}
<script>
    function updateClock() {
        const now = new Date();
        const timeElement = document.getElementById('clock-time');
        const dateElement = document.getElementById('clock-date');

        if (timeElement && dateElement) {
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Meses son 0-11
            const year = now.getFullYear();

            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            dateElement.textContent = `${day}/${month}/${year}`;
        }
    }
    // Actualiza el reloj cada segundo
    setInterval(updateClock, 1000);
    // Llama a la función una vez al cargar para que no haya retraso
    updateClock();
</script>
{{-- ===== FIN DEL SCRIPT DEL RELOJ ===== --}}
{{-- AÑADIR ESTA LÍNEA --}}
@stack('scripts')

</body>
</html>
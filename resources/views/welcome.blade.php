<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Grand Hotels Lux Seguridad</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            body { 
                font-family: 'Inter', sans-serif;
                background-color: #1e293b;
                color: #f8f9fa;
            }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center vh-100">
        <div class="text-center">
            <i class="bi bi-shield-check" style="font-size: 6rem;"></i>
            <h1 class="display-4 fw-bold mt-4">Grand Hotels Lux Seguridad</h1>
            <p class="lead col-md-8 mx-auto">Plataforma de gestión y comando centralizado para el departamento de seguridad.</p>
            <hr class="my-4">
            @if (Route::has('login'))
                <div class="mt-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-lg btn-light">Ir al Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-lg btn-primary">Iniciar Sesión</a>

                        @if (Route::has('register'))
                            {{-- Podríamos habilitar el registro si fuera necesario --}}
                            {{-- <a href="{{ route('register') }}" class="btn btn-lg btn-outline-light ms-2">Registrarse</a> --}}
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>
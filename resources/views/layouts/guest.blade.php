<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Grand Hotels Lux Seguridad</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Custom CSS -->
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f1f5f9;
            }
            .auth-card {
                max-width: 450px;
                margin: 5rem auto;
                border: none;
                border-radius: 1rem;
            }
            .auth-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
            }
            .auth-header i {
                font-size: 3rem;
                color: #1e293b;
            }
            .auth-header h2 {
                font-weight: 700;
                color: #1e293b;
            }
        </style>
    </head>
    <body>
        <div class="container">
            {{ $slot }}
        </div>
    </body>
</html>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Canales Privados</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    Contactos
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($usuariosParaChatear as $usuario)
                        <a href="{{ route('chat.show', $usuario->id) }}" class="list-group-item list-group-item-action">
                            <strong>{{ $usuario->name }}</strong>
                            <small class="d-block text-muted">{{ $usuario->rol }}</small>
                        </a>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center" style="padding: 5rem 0;">
                    <i class="bi bi-chat-dots-fill" style="font-size: 4rem; color: #e0e0e0;"></i>
                    <h5 class="mt-3">Seleccione un contacto para comenzar a chatear.</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
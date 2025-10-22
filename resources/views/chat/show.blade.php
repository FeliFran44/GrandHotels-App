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
                        <a href="{{ route('chat.show', $usuario->id) }}" class="list-group-item list-group-item-action {{ $otroParticipante->id == $usuario->id ? 'active' : '' }}">
                            <strong>{{ $usuario->name }}</strong>
                            <small class="d-block text-muted-light">{{ $usuario->rol }}</small>
                        </a>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    Conversación con <strong>{{ $otroParticipante->name }}</strong>
                </div>
                <div class="card-body" style="height: 60vh; overflow-y: auto; display: flex; flex-direction: column-reverse;">
                    <div>
                        @forelse($conversacion->mensajes as $mensaje)
                            <div class="d-flex mb-3 {{ $mensaje->user_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="card w-75 {{ $mensaje->user_id == Auth::id() ? 'bg-primary text-white' : '' }}">
                                    <div class="card-body p-2">
                                        {{-- Mostramos el cuerpo del mensaje si existe --}}
                                        @if($mensaje->cuerpo)
                                            <p class="card-text mb-1">{{ $mensaje->cuerpo }}</p>
                                        @endif

                                        {{-- Mostramos los archivos adjuntos si existen --}}
                                        @if($mensaje->archivos->isNotEmpty())
                                            <ul class="list-unstyled mb-1">
                                                @foreach($mensaje->archivos as $archivo)
                                                <li>
                                                    <a href="{{ Storage::url($archivo->path) }}" target="_blank" class="{{ $mensaje->user_id == Auth::id() ? 'text-white' : 'text-primary' }}">
                                                        <i class="bi bi-file-earmark-arrow-down"></i> {{ $archivo->nombre_original }}
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <small class="text-muted {{ $mensaje->user_id == Auth::id() ? 'text-white-50' : '' }}">{{ $mensaje->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Aún no hay mensajes. ¡Envía el primero!</p>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer">
                    @isset($mensajes)
                    <div class="d-flex justify-content-center mb-2">
                        {{ $mensajes->appends(request()->query())->links() }}
                    </div>
                    @endisset
                    <form action="{{ route('chat.storeMessage', $conversacion) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="cuerpo" class="form-control" placeholder="Escribe tu mensaje...">
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <label for="archivos-chat" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-paperclip"></i> Adjuntar
                                </label>
                                <input type="file" id="archivos-chat" name="archivos[]" multiple class="d-none">
                            </div>
                            <button class="btn btn-primary" type="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

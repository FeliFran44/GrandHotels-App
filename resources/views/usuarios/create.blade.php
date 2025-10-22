@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4">Crear Usuario</h1>
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif
  <div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('usuarios.store') }}" method="POST">
      @include('usuarios.form')
      <div class="mt-3"><button class="btn btn-primary">Guardar</button> <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a></div>
    </form>
  </div></div>
</div>
@endsection


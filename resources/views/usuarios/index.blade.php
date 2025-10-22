@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Usuarios</h1>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Crear Usuario</a>
  </div>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Hotel</th><th>Secciones</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            @forelse($usuarios as $u)
              <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->rol }}</td>
                <td>{{ $u->hotel?->nombre ?? '—' }}</td>
                <td class="small">{{ $u->permisos ? implode(', ', $u->permisos) : '—' }}</td>
                <td class="d-flex">
                  <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil"></i></a>
                  <form action="{{ route('usuarios.destroy', $u) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">Sin usuarios</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">{{ $usuarios->links() }}</div>
    </div>
  </div>
</div>
@endsection


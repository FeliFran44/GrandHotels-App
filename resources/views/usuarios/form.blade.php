@csrf
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nombre</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Email (@ghl.com)</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Rol</label>
    <select name="rol" class="form-select" required id="rol-select">
      @php $rolSel = old('rol', $usuario->rol ?? 'Gerente'); @endphp
      <option value="Coordinador" {{ $rolSel==='Coordinador'?'selected':'' }}>Coordinador</option>
      <option value="Gerente" {{ $rolSel==='Gerente'?'selected':'' }}>Gerente</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Hotel (si es Gerente)</label>
    <select name="hotel_id" class="form-select">
      <option value="">—</option>
      @foreach($hoteles as $h)
        <option value="{{ $h->id }}" {{ (string)old('hotel_id', $usuario->hotel_id ?? '') === (string)$h->id ? 'selected':'' }}>{{ $h->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Contraseña {{ isset($usuario)?'(dejar vacío para no cambiar)':'' }}</label>
    <input type="password" name="password" class="form-control" {{ isset($usuario)?'':'required' }}>
    <small class="text-muted">Mínimo 8 caracteres</small>
  </div>
  <div class="col-md-4">
    <label class="form-label">Confirmar Contraseña</label>
    <input type="password" name="password_confirmation" class="form-control" {{ isset($usuario)?'':'required' }}>
  </div>
</div>

<hr>
<h5>Secciones permitidas</h5>
<div class="row row-cols-2 row-cols-md-3 g-2">
  @php $perms = collect(old('permisos', $usuario->permisos ?? [])); @endphp
  @foreach($sections as $sec)
  <div class="col">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input me-1" name="permisos[]" value="{{ $sec }}" {{ $perms->contains($sec)?'checked':'' }}>
      {{ ucfirst(str_replace('_',' ', $sec)) }}
    </label>
  </div>
  @endforeach
</div>


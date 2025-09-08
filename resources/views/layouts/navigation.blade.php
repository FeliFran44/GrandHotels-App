<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
            <i class="bi bi-shield-check me-2 fs-4"></i>
            <span class="fs-5">GHL Seguridad</span>
        </a>
    </div>
    
    <div id="clock-container" class="p-2 my-2 text-center text-white">
        <div id="clock-time" class="fs-2 fw-bold"></div>
        <div id="clock-date"></div>
    </div>
    
    <ul class="nav nav-pills flex-column" style="flex-grow: 1;">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-grid me-2"></i> Dashboard
            </a>
        </li>
        @if (Auth::user()->rol === 'Coordinador')
        <li class="nav-item">
            <a href="{{ route('hoteles.index') }}" class="nav-link {{ request()->routeIs('hoteles.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-building me-2"></i> Hoteles
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a href="{{ route('personal.index') }}" class="nav-link {{ request()->routeIs('personal.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-people me-2"></i> Personal
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('comunicados.index') }}" class="nav-link {{ request()->routeIs('comunicados.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-chat-left-text me-2"></i> Comunicados
            </a>
        </li>
        <li class="nav-item">
    <a href="{{ route('planificacion.index') }}" class="nav-link {{ request()->routeIs('planificacion.*') ? 'active' : 'text-white' }}">
        <i class="bi bi-calendar-event me-2"></i> Planificación
    </a>
</li>
        <li class="nav-item">
            <a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-tools me-2"></i> Inventario
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('accidentes.index') }}" class="nav-link {{ request()->routeIs('accidentes.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-exclamation-octagon me-2"></i> Accidentes
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : 'text-white' }} position-relative">
                <i class="bi bi-chat-quote-fill me-2"></i> 
                Chats Privados
                {{-- --- CONTADOR DE NOTIFICACIONES --- --}}
                @if(isset($mensajesNoLeidosCount) && $mensajesNoLeidosCount > 0)
                    <span class="position-absolute top-50 start-100 translate-middle badge rounded-pill bg-primary">
                        {{ $mensajesNoLeidosCount }}
                    </span>
                @endif
            </a>
        </li>
        @if (Auth::user()->rol === 'Coordinador')
        <li class="nav-item">
            <a href="{{ route('archivo-general.index') }}" class="nav-link {{ request()->routeIs('archivo-general.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-archive-fill me-2"></i> Archivo General
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit-log.index') }}" class="nav-link {{ request()->routeIs('audit-log.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-shield-lock-fill me-2"></i> Bitácora de Auditoría
            </a>
        </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-block">{{ Auth::user()->name }}</strong>
                <small class="text-muted">{{ Auth::user()->rol }}</small>
            </div>
            @if (Auth::user()->rol === 'Coordinador' && isset($mantenimientosPendientes) && $mantenimientosPendientes->count() > 0)
            <div class="dropdown">
                <a href="#" class="link-light text-decoration-none position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em;">
                        {{ $mantenimientosPendientes->count() }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                    <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="dropdown-header mb-0">Alertas de Mantenimiento</h6>
                    </li>
                    @foreach ($mantenimientosPendientes as $item)
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-2" href="{{ route('inventario.edit', $item) }}">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-3 mt-1"></i>
                                <div>
                                    <strong>{{ $item->nombre }}</strong> ({{ $item->hotel->nombre }})
                                    <small class="d-block text-muted">Vence en {{ \Carbon\Carbon::now()->diffInDays($item->proxima_fecha_mantenimiento, false) }} días ({{ $item->proxima_fecha_mantenimiento->format('d/m/Y') }})</small>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
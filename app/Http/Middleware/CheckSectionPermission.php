<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionPermission
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        // Coordinador siempre permitido
        if ($user->rol === 'Coordinador') {
            return $next($request);
        }
        // Para otros roles, revisar lista de permisos (JSON en users.permisos)
        $permisos = (array) ($user->permisos ?? []);
        if (in_array($section, $permisos, true)) {
            return $next($request);
        }
        abort(403, 'No tiene permiso para esta sección.');
    }
}


<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCoordinatorRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->rol, ['Coordinador', 'Fantasma'])) {
            return $next($request);
        }
        abort(403, 'Acción no autorizada.');
    }
}
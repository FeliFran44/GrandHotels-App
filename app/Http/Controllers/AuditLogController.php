<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filtrado por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtrado por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrado por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $auditLogs = $query->paginate(25); // Paginamos los resultados
        $users = User::orderBy('name')->get(); // Para el selector de usuarios

        return view('audit.index', [
            'auditLogs' => $auditLogs,
            'users' => $users,
            'filters' => $request->all(), // Pasamos los filtros a la vista
        ]);
    }
}
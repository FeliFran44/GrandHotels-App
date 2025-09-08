<?php

namespace App\Observers;

use App\Models\Inventario;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class InventarioObserver
{
    public function created(Inventario $inventario): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => Inventario::class,
            'model_id' => $inventario->id,
            'new_values' => $inventario->toArray(), // CORREGIDO
        ]);
    }

    public function updated(Inventario $inventario): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => Inventario::class,
            'model_id' => $inventario->id,
            'old_values' => $inventario->getOriginal(), // CORREGIDO
            'new_values' => $inventario->getChanges(),   // CORREGIDO
        ]);
    }

    public function deleted(Inventario $inventario): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => Inventario::class,
            'model_id' => $inventario->id,
            'old_values' => $inventario->toArray(), // CORREGIDO
        ]);
    }
}
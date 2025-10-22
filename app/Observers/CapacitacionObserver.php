<?php

namespace App\Observers;

use App\Models\Capacitacion;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CapacitacionObserver
{
    public function created(Capacitacion $capacitacion): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => Capacitacion::class,
            'model_id' => $capacitacion->id,
            'new_values' => $capacitacion->toArray(),
        ]);
    }

    public function updated(Capacitacion $capacitacion): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => Capacitacion::class,
            'model_id' => $capacitacion->id,
            'old_values' => $capacitacion->getOriginal(),
            'new_values' => $capacitacion->getChanges(),
        ]);
    }

    public function deleted(Capacitacion $capacitacion): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => Capacitacion::class,
            'model_id' => $capacitacion->id,
            'old_values' => $capacitacion->toArray(),
        ]);
    }
}

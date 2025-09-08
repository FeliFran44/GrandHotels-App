<?php

namespace App\Observers;

use App\Models\PersonalSeguridad;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PersonalSeguridadObserver
{
    public function created(PersonalSeguridad $personal): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => PersonalSeguridad::class,
            'model_id' => $personal->id,
            'new_values' => $personal->toArray(), // CORREGIDO
        ]);
    }

    public function updated(PersonalSeguridad $personal): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => PersonalSeguridad::class,
            'model_id' => $personal->id,
            'old_values' => $personal->getOriginal(), // CORREGIDO
            'new_values' => $personal->getChanges(),   // CORREGIDO
        ]);
    }

    public function deleted(PersonalSeguridad $personal): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => PersonalSeguridad::class,
            'model_id' => $personal->id,
            'old_values' => $personal->toArray(), // CORREGIDO
        ]);
    }
}
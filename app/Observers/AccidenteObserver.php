<?php

namespace App\Observers;

use App\Models\Accidente;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AccidenteObserver
{
    public function created(Accidente $accidente): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => Accidente::class,
            'model_id' => $accidente->id,
            'new_values' => $accidente->toArray(), // CORREGIDO
        ]);
    }

    public function updated(Accidente $accidente): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => Accidente::class,
            'model_id' => $accidente->id,
            'old_values' => $accidente->getOriginal(), // CORREGIDO
            'new_values' => $accidente->getChanges(),   // CORREGIDO
        ]);
    }

    public function deleted(Accidente $accidente): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => Accidente::class,
            'model_id' => $accidente->id,
            'old_values' => $accidente->toArray(), // CORREGIDO
        ]);
    }
}
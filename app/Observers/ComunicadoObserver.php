<?php

namespace App\Observers;

use App\Models\Comunicado;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ComunicadoObserver
{
    public function created(Comunicado $comunicado): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => Comunicado::class,
            'model_id' => $comunicado->id,
            'new_values' => $comunicado->toArray(), // CORREGIDO
        ]);
    }
}
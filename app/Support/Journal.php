<?php

namespace App\Support;

use App\Models\Activite;

class Journal
{
    public static function noter(string $action, string $module, ?string $reference = null, ?string $detail = null): void
    {
        if (! auth()->check()) {
            return;
        }

        Activite::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'reference' => $reference,
            'detail' => $detail ? mb_substr($detail, 0, 500) : null,
        ]);
    }
}

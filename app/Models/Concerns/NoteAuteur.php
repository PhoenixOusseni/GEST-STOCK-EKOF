<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait NoteAuteur
{
    public static function bootNoteAuteur(): void
    {
        static::creating(function ($model) {
            if (empty($model->user_id) && auth()->id()) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nomAuteur(): string
    {
        $user = $this->auteur;

        if (! $user) {
            return 'Non renseigné';
        }

        return $user->nomComplet();
    }
}

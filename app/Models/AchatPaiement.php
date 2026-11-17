<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchatPaiement extends Model
{
    protected $fillable = [
        'achat_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function achat()
    {
        return $this->belongsTo(Achat::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentePaiement extends Model
{
    protected $fillable = [
        'vente_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
}

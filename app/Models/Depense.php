<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = [
        'date_depense',
        'libelle',
        'categorie',
        'montant',
        'mode_paiement',
        'reference',
        'observation',
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2',
    ];

    public const CATEGORIES = [
        'Loyer',
        'Salaires',
        'Transport',
        'Fournitures',
        'Electricité / Eau',
        'Communication',
        'Entretien & Réparations',
        'Marketing',
        'Impôts & Taxes',
        'Divers',
    ];
}

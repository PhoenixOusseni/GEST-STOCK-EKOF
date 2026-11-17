<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $guarded = [

    ];

    /**
     * Relation avec les entrées
     */
    public function entrees()
    {
        return $this->hasMany(Entree::class);
    }

    /**
     * Relation avec les achats
     */
    public function achats()
    {
        return $this->hasMany(Achat::class);
    }
}

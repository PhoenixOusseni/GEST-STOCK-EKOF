<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [
        
    ];

    /**
     * Relation avec les sorties
     */
    public function sorties()
    {
        return $this->hasMany(Sortie::class);
    }

    /**
     * Relation avec les ventes
     */
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }
}

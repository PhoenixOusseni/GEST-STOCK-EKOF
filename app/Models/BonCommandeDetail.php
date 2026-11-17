<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BonCommandeDetail extends Model
{
    use HasFactory;

    protected $table = 'bon_commande_details';

    protected $fillable = [
        'bon_commande_id',
        'article_id',
        'depot_id',
        'quantite',
        'prix_vente',
        'prix_total',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'prix_total' => 'decimal:2',
    ];

    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchatDetail extends Model
{
    protected $fillable = [
        'achat_id',
        'article_id',
        'depot_id',
        'quantite',
        'prix_unitaire',
        'prix_total',
    ];

    protected $casts = [
        'quantite'      => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'prix_total'    => 'decimal:2',
    ];

    public function achat()
    {
        return $this->belongsTo(Achat::class);
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

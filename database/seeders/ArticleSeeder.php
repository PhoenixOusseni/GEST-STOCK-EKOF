<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            ['code' => 'ART-00001', 'designation' => 'Riz parfumé 25 kg', 'prix_achat' => 14000, 'prix_vente' => 17500],
            ['code' => 'ART-00002', 'designation' => 'Huile végétale 5 L', 'prix_achat' => 6500, 'prix_vente' => 8000],
            ['code' => 'ART-00003', 'designation' => 'Sucre en poudre 1 kg', 'prix_achat' => 700, 'prix_vente' => 900],
            ['code' => 'ART-00004', 'designation' => 'Lait en poudre 400 g', 'prix_achat' => 2500, 'prix_vente' => 3200],
            ['code' => 'ART-00005', 'designation' => 'Savon de ménage', 'prix_achat' => 300, 'prix_vente' => 500],
            ['code' => 'ART-00006', 'designation' => 'Farine de blé 10 kg', 'prix_achat' => 5500, 'prix_vente' => 7000],
            ['code' => 'ART-00007', 'designation' => 'Tomate concentrée 400 g', 'prix_achat' => 450, 'prix_vente' => 650],
            ['code' => 'ART-00008', 'designation' => 'Spaghetti 500 g', 'prix_achat' => 400, 'prix_vente' => 600],
            ['code' => 'ART-00009', 'designation' => 'Eau minérale 1,5 L', 'prix_achat' => 250, 'prix_vente' => 400],
            ['code' => 'ART-00010', 'designation' => 'Café moulu 250 g', 'prix_achat' => 1800, 'prix_vente' => 2500],
        ];

        foreach ($articles as $article) {
            DB::table('articles')->updateOrInsert(
                ['code' => $article['code']],
                $article + [
                    'stock' => 0,
                    'seuil' => 10,
                    'reference' => null,
                    'code_barre' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = [
            ['code' => 'FRS-00001', 'raison_sociale' => 'Société Céréales du Faso', 'nom' => 'Abdoulaye Zan', 'telephone' => '25001122'],
            ['code' => 'FRS-00002', 'raison_sociale' => 'Huilerie Sahel', 'nom' => 'Awa Diallo', 'telephone' => '25012233'],
            ['code' => 'FRS-00003', 'raison_sociale' => 'Grossiste Sankara', 'nom' => 'Ibrahim Sankara', 'telephone' => '25023344'],
            ['code' => 'FRS-00004', 'raison_sociale' => 'Boissons du Centre', 'nom' => 'Clarisse Nikiéma', 'telephone' => '25034455'],
            ['code' => 'FRS-00005', 'raison_sociale' => 'Import Foods BF', 'nom' => 'Jean-Baptiste Ilboudo', 'telephone' => '25045566'],
        ];

        foreach ($fournisseurs as $fournisseur) {
            DB::table('fournisseurs')->updateOrInsert(
                ['code' => $fournisseur['code']],
                $fournisseur + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

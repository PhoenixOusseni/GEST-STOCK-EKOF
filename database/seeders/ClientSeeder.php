<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['code' => 'CLT-00001', 'raison_sociale' => 'Boutique Wend-Panga', 'nom' => 'Aminata Ouédraogo', 'telephone' => '70011223'],
            ['code' => 'CLT-00002', 'raison_sociale' => 'Alimentation du Marché', 'nom' => 'Issa Sawadogo', 'telephone' => '76022334'],
            ['code' => 'CLT-00003', 'raison_sociale' => null, 'nom' => 'Mariam Kaboré', 'telephone' => '65033445'],
            ['code' => 'CLT-00004', 'raison_sociale' => 'Superette Naaba', 'nom' => 'Paul Compaoré', 'telephone' => '78044556'],
            ['code' => 'CLT-00005', 'raison_sociale' => null, 'nom' => 'Fatou Traoré', 'telephone' => '64055667'],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->updateOrInsert(
                ['code' => $client['code']],
                $client + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

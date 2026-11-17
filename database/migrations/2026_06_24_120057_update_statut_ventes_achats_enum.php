<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étendre l'enum pour accepter toutes les valeurs temporairement
        \DB::statement("ALTER TABLE ventes MODIFY COLUMN statut ENUM('brouillon','validee','annulee','non_solde','solde') NOT NULL DEFAULT 'non_solde'");
        // Convertir les anciennes valeurs
        \DB::statement("UPDATE ventes SET statut = CASE WHEN reste_a_payer <= 0 THEN 'solde' ELSE 'non_solde' END");
        // Réduire l'enum aux nouvelles valeurs
        \DB::statement("ALTER TABLE ventes MODIFY COLUMN statut ENUM('non_solde','solde') NOT NULL DEFAULT 'non_solde'");

        \DB::statement("ALTER TABLE achats MODIFY COLUMN statut ENUM('brouillon','valide','non_solde','solde') NOT NULL DEFAULT 'non_solde'");
        \DB::statement("UPDATE achats SET statut = CASE WHEN reste_a_payer <= 0 THEN 'solde' ELSE 'non_solde' END");
        \DB::statement("ALTER TABLE achats MODIFY COLUMN statut ENUM('non_solde','solde') NOT NULL DEFAULT 'non_solde'");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE ventes MODIFY COLUMN statut ENUM('brouillon','validee','annulee') NOT NULL DEFAULT 'brouillon'");
        \DB::statement("ALTER TABLE achats MODIFY COLUMN statut ENUM('brouillon','valide') NOT NULL DEFAULT 'brouillon'");
    }
};

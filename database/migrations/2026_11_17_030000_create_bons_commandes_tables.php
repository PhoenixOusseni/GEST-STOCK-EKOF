<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bon')->unique();
            $table->date('date_commande');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('type_vente')->default('detail');
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->decimal('reste_a_payer', 15, 2)->default(0);
            $table->string('statut')->default('en_cours');
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bon_commande_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commandes')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('depot_id')->constrained('depots')->cascadeOnDelete();
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_vente', 15, 2);
            $table->decimal('prix_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_commande_details');
        Schema::dropIfExists('bons_commandes');
    }
};

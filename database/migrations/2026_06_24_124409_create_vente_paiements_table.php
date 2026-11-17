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
        Schema::create('vente_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->decimal('montant', 15, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Migrer les paiements initiaux existants
        \DB::statement("
            INSERT INTO vente_paiements (vente_id, montant, date_paiement, mode_paiement, created_at, updated_at)
            SELECT id, montant_paye, date_vente, 'Versement initial', NOW(), NOW()
            FROM ventes WHERE montant_paye > 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_paiements');
    }
};

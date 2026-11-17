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
        Schema::create('achat_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achat_id')->constrained('achats')->cascadeOnDelete();
            $table->decimal('montant', 15, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Migrer les paiements initiaux existants
        \DB::statement("
            INSERT INTO achat_paiements (achat_id, montant, date_paiement, mode_paiement, created_at, updated_at)
            SELECT id, montant_paye, date_achat, 'Versement initial', NOW(), NOW()
            FROM achats WHERE montant_paye > 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_paiements');
    }
};

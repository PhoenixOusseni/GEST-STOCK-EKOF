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
        Schema::table('ventes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->decimal('montant_paye', 15, 2)->default(0)->after('montant_total');
            $table->decimal('reste_a_payer', 15, 2)->default(0)->after('montant_paye');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->dropColumn(['montant_paye', 'reste_a_payer']);
        });
    }
};

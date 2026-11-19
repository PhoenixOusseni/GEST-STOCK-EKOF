<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'ventes',
            'bons_commandes',
            'entrees',
            'sorties',
            'achats',
            'depenses',
            'encaissements',
            'decaissements',
            'transferts',
        ] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->string('module', 60);
            $table->string('reference')->nullable();
            $table->string('detail', 500)->nullable();
            $table->timestamps();
            $table->index(['created_at', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');

        foreach ([
            'ventes',
            'bons_commandes',
            'entrees',
            'sorties',
            'achats',
            'depenses',
            'encaissements',
            'decaissements',
            'transferts',
        ] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};

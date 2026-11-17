<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->foreignId('depot_id')->nullable()->after('id')->constrained('depots')->nullOnDelete();
        });

        $depotId = DB::table('depots')->orderBy('id')->value('id');
        if ($depotId) {
            DB::table('depenses')->whereNull('depot_id')->update(['depot_id' => $depotId]);
        }
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('depot_id');
        });
    }
};

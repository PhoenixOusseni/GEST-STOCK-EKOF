<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('depot_id')->nullable()->after('role')->constrained('depots')->nullOnDelete();
        });

        DB::table('users')->whereIn('role', ['super_admin', 'admin'])->update(['role' => 'administrateur']);
        DB::table('users')->whereIn('role', ['user', 'manager'])->update(['role' => 'caisse']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('depot_id');
        });

        DB::table('users')->where('role', 'administrateur')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'caisse')->update(['role' => 'user']);
        DB::table('users')->where('role', 'magazin')->update(['role' => 'user']);
    }
};

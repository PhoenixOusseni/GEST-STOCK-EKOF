<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nom' => 'Super',
                'prenom' => 'Admin',
                'telephone' => '67186063',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@material.com',
            'password' => Hash::make('secret'),
            'role' => 'admin'
        ]);

        DB::table('pengaturan_absensis')->insert([
            'check_in' => '00:00',
            'check_out' => '00:00',
            'rentang_awal_IP' => '000.000.000.000',
            'rentang_akhir_IP' => '999.999.999.999', 
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

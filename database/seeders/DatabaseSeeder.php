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
        DB::table('pengaturan_absensis')->insert([
            'check_in' => '00:00',
            'check_out' => '00:00',
            'rentang_awal_IP' => '000.000.000.000',
            'rentang_akhir_IP' => '999.999.999.999', 
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('jabatan_organisasis')->insert([
            [
            'nama_jabatan' => 'Marketing',
            'besaran_gaji' =>  2500000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'nama_jabatan' => 'Kerajinan',
            'besaran_gaji'=> 2500000,
             'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'nama_jabatan' => 'Office Boy',
            'besaran_gaji'=> 1500000,
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
            'nama_jabatan' => 'Keamanan',
            'besaran_gaji' => 1500000,
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
            'nama_jabatan' => 'Chef',
            'besaran_gaji'=> 2000000,
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
            'nama_jabatan' => 'Admin',
            'besaran_gaji' => 2500000,
            'created_at' => now(),
            'updated_at' => now(),
            ]

        ]);

        // Buat user dengan jabatan Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@material.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
        ]);
    }
}

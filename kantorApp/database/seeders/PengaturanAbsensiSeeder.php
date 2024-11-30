<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\PengaturanAbsensi;
use Illuminate\Support\Facades\DB;
class PengaturanAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pengaturan_absensis')->insert([
            'waktu_buka' => '00:00',
            'waktu_tutup' => '00:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

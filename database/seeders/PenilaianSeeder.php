<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenilaianSeeder extends Seeder
{
    public function run()
    {
        $penilaian = [
            // A1 (Nurwanto) - C1:500, C2:70, C3:10, C4:80, C5:3000
            ['alternatif_id' => 1, 'kriteria_id' => 1, 'nilai' => 500, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 1, 'kriteria_id' => 2, 'nilai' => 70, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 1, 'kriteria_id' => 3, 'nilai' => 10, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 1, 'kriteria_id' => 4, 'nilai' => 80, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 1, 'kriteria_id' => 5, 'nilai' => 3000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // A2 (Bambang) - C1:300, C2:90, C3:10, C4:60, C5:2500
            ['alternatif_id' => 2, 'kriteria_id' => 1, 'nilai' => 300, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 2, 'kriteria_id' => 2, 'nilai' => 90, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 2, 'kriteria_id' => 3, 'nilai' => 10, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 2, 'kriteria_id' => 4, 'nilai' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 2, 'kriteria_id' => 5, 'nilai' => 2500, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // A3 (Sukri) - C1:400, C2:80, C3:9, C4:90, C5:2000
            ['alternatif_id' => 3, 'kriteria_id' => 1, 'nilai' => 400, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 3, 'kriteria_id' => 2, 'nilai' => 80, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 3, 'kriteria_id' => 3, 'nilai' => 9, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 3, 'kriteria_id' => 4, 'nilai' => 90, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 3, 'kriteria_id' => 5, 'nilai' => 2000, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // A4 (Ayu) - C1:450, C2:70, C3:8, C4:50, C5:1500
            ['alternatif_id' => 4, 'kriteria_id' => 1, 'nilai' => 450, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 4, 'kriteria_id' => 2, 'nilai' => 70, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 4, 'kriteria_id' => 3, 'nilai' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 4, 'kriteria_id' => 4, 'nilai' => 50, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['alternatif_id' => 4, 'kriteria_id' => 5, 'nilai' => 1500, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('penilaian')->insert($penilaian);
    }
}
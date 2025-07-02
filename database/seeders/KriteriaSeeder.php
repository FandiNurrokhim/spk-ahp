<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        $kriteria = [
            [
                'nama' => 'Penghasilan dibawah UMK',
                'bobot' => 5,
                'jenis' => 'cost', // Cost karena penghasilan rendah = prioritas tinggi
                'kode' => 'C1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'WNII',
                'bobot' => 4,
                'jenis' => 'benefit', // Benefit karena WNI = prioritas
                'kode' => 'C2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Sudah berkeluarga',
                'bobot' => 4,
                'jenis' => 'benefit', // Benefit karena berkeluarga = prioritas
                'kode' => 'C3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Memiliki Rumah Tidak Layak',
                'bobot' => 3,
                'jenis' => 'benefit', // Benefit karena rumah tidak layak = perlu bantuan
                'kode' => 'C4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama' => 'Menguasai Tanah',
                'bobot' => 2,
                'jenis' => 'benefit', // Benefit karena punya tanah = prioritas
                'kode' => 'C5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('kriteria')->insert($kriteria);
    }
}
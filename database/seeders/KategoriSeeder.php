<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kategori::create([
            "nama" => "Akademik",
        ]);
        Kategori::create([
            "nama" => "Non Akademik",
        ]);
        Kategori::create([
            "nama" => "Psikologis",
        ]);
        Kategori::create([
            "nama" => "Kepribadian",
        ]);
    }
}

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Alternatif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlternatifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $namaAlternatif = [
            'Indah Nur Paraswati',
            'Fahcri Taufiqurrahman',
            'Shendi Teuku Maulana Efendi',
            'Rangga Ranubaya',
            'Rahaditya Rizky Sutopo Putri',
            'Seviannanda Kurniawan',
        ];

        foreach ($namaAlternatif as $nama) {
            Alternatif::create([
                'nama' => $nama,
                'nisn' => rand(1000000000, 9999999999),
                'tanggal_lahir' => now()->subYears(rand(15, 20))->format('Y-m-d'),
                'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                'alamat' => 'Alamat ' . $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->add_penilaian_alternatif();
        $this->perhitungan_alternatif();
    }

    public function add_penilaian_alternatif()
    {
        $alternatif = Alternatif::orderBy('id', 'asc')->get();
        $kriteria = Kriteria::orderBy('id', 'asc')->get();

        foreach ($alternatif as $item) {
            foreach ($kriteria as $value) {
                Penilaian::updateOrCreate(
                    ['alternatif_id' => $item->id, 'kriteria_id' => $value->id],
                    [
                        'nilai' => rand(50, 100), 
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
    }

    public function perhitungan_alternatif()
    {
        $penilaian = Penilaian::orderBy('id', 'asc')->get();
        $matriksNilaiKriteria = DB::table('matriks_nilai_prioritas_utama')->get();
    
        foreach ($penilaian->unique('alternatif_id') as $item) {
            $nilai = 0;
            foreach ($penilaian->where('alternatif_id', $item->alternatif_id) as $value) {
                $kriteria = $matriksNilaiKriteria->where('kriteria_id', $value->kriteria_id)->first()->prioritas ?? 0;
                $nilai += $kriteria * $value->nilai;
            }
    
            DB::table('hasil_solusi_ahp')->insert([
                'nilai' => $nilai,
                'alternatif_id' => $item->alternatif_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

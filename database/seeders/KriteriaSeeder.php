<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Kriteria;
use App\Models\Alternatif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteriaData = [
            ["kode" => "MP00001", "nama" => "Matematika"],
            ["kode" => "MP00002", "nama" => "Bahasa Indonesia"],
            ["kode" => "MP00003", "nama" => "Bahasa Inggris"],
            ["kode" => "MP00004", "nama" => "IPA"],
            ["kode" => "MP00005", "nama" => "IPS"],
            ["kode" => "MP00006", "nama" => "Agama"],
            ["kode" => "MP00007", "nama" => "Pendidikan Pancasila"],
            ["kode" => "MP00008", "nama" => "Olahraga"],
        ];

        // Loop untuk insert
        foreach ($kriteriaData as $data) {
            Kriteria::create([
                "kode" => $data['kode'],
                "nama" => $data['nama'],
            ]);
        }


        $kriteria = Kriteria::orderBy('id')->get();

        // Nilai perbandingan berpasangan (symmetric reciprocal)
        // Diisi hanya untuk segitiga atas, sisanya dihitung sebagai kebalikannya (1/nilai)
        $nilaiMatrix = [
            // Matematika
            [1,    3,    4,    5,    4,    2,    2,    3],
            // B. Indonesia
            [0,    1,    2,    3,    2,    2,    1,    2],
            // B. Inggris
            [0,    0,    1,    2,    1,    2,    1,    2],
            // IPA
            [0,    0,    0,    1,    2,    3,    2,    3],
            // IPS
            [0,    0,    0,    0,    1,    2,    2,    2],
            // Agama
            [0,    0,    0,    0,    0,    1,    2,    2],
            // PPKN
            [0,    0,    0,    0,    0,    0,    1,    2],
            // Olahraga
            [0,    0,    0,    0,    0,    0,    0,    1],
        ];

        // Insert ke tabel matriks_perbandingan_utama
        foreach ($kriteria as $i => $kritA) {
            foreach ($kriteria as $j => $kritB) {
                if ($i == $j) {
                    $nilai = 1;
                } elseif ($i < $j) {
                    $nilai = $nilaiMatrix[$i][$j];
                } else {
                    $nilai = 1 / $nilaiMatrix[$j][$i];
                }

                DB::table('matriks_perbandingan_utama')->insert([
                    'nilai' => $nilai,
                    'kriteria_id' => $kritA->id,
                    'kriteria_id_banding' => $kritB->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // $this->add_matriks_perbandingan();
        // $this->add_penilaian_alternatif();
        $this->matriks_nilai_utama();
        $this->matriks_penjumlahan_utama();
    }

    public function add_matriks_perbandingan()
    {
        $kriteria = Kriteria::orderBy('id', 'asc')->get();
        foreach ($kriteria as $item) {
            foreach ($kriteria as $value) {
                if ($item->id == $value->id) {
                    DB::table('matriks_perbandingan_utama')->insert([
                        'nilai' => 1,
                        'kriteria_id' => $item->id,
                        'kriteria_id_banding' => $value->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    DB::table('matriks_perbandingan_utama')->insert([
                        'nilai' => null,
                        'kriteria_id' => $item->id,
                        'kriteria_id_banding' => $value->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }

    public function add_penilaian_alternatif()
    {
        $alternatif = Alternatif::all();
        $kriteria = Kriteria::orderBy('id', 'asc')->get();
        foreach ($kriteria as $value) {
            foreach ($alternatif as $item) {
                $penilaian = DB::table('penilaian')->where('alternatif_id', $item->id)->where('kriteria_id', $value->id)->first();
                if ($penilaian == null) {
                    DB::table('penilaian')->insert([
                        'alternatif_id' => $item->id,
                        'kriteria_id' => $value->id,
                        'sub_kriteria_id' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }

    public function matriks_nilai_utama()
    {
        $matriksPerbandingan = DB::table('matriks_perbandingan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->orderBy('mpu.kriteria_id', 'asc')
            ->orderBy('mpu.kriteria_id_banding', 'asc')
            ->get();

        $kriteria = Kriteria::orderBy('id', 'asc')->get();

        // $dataNilai = [];
        DB::table('matriks_nilai_utama')->truncate();
        DB::table('matriks_nilai_prioritas_utama')->truncate();
        foreach ($matriksPerbandingan as $item) {
            $jumlahNilai = $matriksPerbandingan->where('kriteria_id_banding', $item->kriteria_id_banding)->sum('nilai');
            // $dataNilai[] = [
            //     'nilai_banding' => $item->nilai,
            //     'jumlah_banding' => $jumlahNilai,
            //     'nilai' => $item->nilai / $jumlahNilai,
            //     'kriteria_id' => $item->kriteria_id,
            //     'kriteria_id_banding' => $item->kriteria_id_banding,
            // ];

            DB::table('matriks_nilai_utama')->insert([
                'nilai' => $item->nilai / $jumlahNilai,
                'kriteria_id' => $item->kriteria_id,
                'kriteria_id_banding' => $item->kriteria_id_banding,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $matriksNilai = DB::table('matriks_nilai_utama as mnu')
            ->join('kriteria as k', 'k.id', '=', 'mnu.kriteria_id')
            ->select('mnu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        foreach ($kriteria as $item) {
            $nilai = $matriksNilai->where('kriteria_id', $item->id)->sum('nilai');
            $jumlahKriteria = $kriteria->count();

            DB::table('matriks_nilai_prioritas_utama')->insert([
                'prioritas' => $nilai / $jumlahKriteria,
                'kriteria_id' => $item->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        // dd($dataNilai);
        // dd($matriksPerbandingan);
    }

    public function matriks_penjumlahan_utama()
    {
        $matriksPerbandingan = DB::table('matriks_perbandingan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->orderBy('mpu.kriteria_id', 'asc')
            ->orderBy('mpu.kriteria_id_banding', 'asc')
            ->get();

        $matriksNilaiPrioritas = DB::table('matriks_nilai_prioritas_utama as mnpu')
            ->join('kriteria as k', 'k.id', '=', 'mnpu.kriteria_id')
            ->select('mnpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        $kriteria = Kriteria::orderBy('id', 'asc')->get();

        // $dataNilai = [];
        DB::table('matriks_penjumlahan_utama')->truncate();
        DB::table('matriks_penjumlahan_prioritas_utama')->truncate();
        foreach ($matriksPerbandingan as $item) {
            $prioritas = $matriksNilaiPrioritas->where('kriteria_id', $item->kriteria_id_banding)->first()->prioritas;
            // $dataNilai[] = [
            //     'nilai_banding' => $item->nilai,
            //     'prioritas_nilai' => $prioritas,
            //     'nilai' => $item->nilai * $prioritas,
            //     'kriteria_id' => $item->kriteria_id,
            //     'kriteria_id_banding' => $item->kriteria_id_banding,
            // ];

            DB::table('matriks_penjumlahan_utama')->insert([
                'nilai' => $item->nilai * $prioritas,
                'kriteria_id' => $item->kriteria_id,
                'kriteria_id_banding' => $item->kriteria_id_banding,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $matriksPenjumlahan = DB::table('matriks_penjumlahan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->orderBy('mpu.kriteria_id', 'asc')
            ->orderBy('mpu.kriteria_id_banding', 'asc')
            ->get();
        // $dataNilai = [];
        foreach ($kriteria as $item) {
            $nilai = $matriksPenjumlahan->where('kriteria_id', $item->id)->sum('nilai');
            $prioritas = $matriksNilaiPrioritas->where('kriteria_id', $item->id)->first()->prioritas;

            // $dataNilai[] = [
            //     'penjumlahan_kriteria' => $nilai,
            //     'prioritas' => $prioritas,
            //     'nilai' => $nilai / $prioritas,
            //     'kriteria_id' => $item->id,
            // ];

            DB::table('matriks_penjumlahan_prioritas_utama')->insert([
                'prioritas' => $nilai / $prioritas,
                'kriteria_id' => $item->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        // dd($dataNilai);
        // dd($matriksPerbandingan);
    }
}

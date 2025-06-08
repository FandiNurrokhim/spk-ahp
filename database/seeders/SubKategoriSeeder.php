<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteria = Kriteria::orderBy('id')->get();
        $namaSubKriteria = ["A", "B", "C", "D"];
        $nilaiSubKriteria = [
            // S1 vs S1, S1 vs S2, S1 vs S3, S1 vs S4
            1,   3,   5,   0.5,
            // S2 vs S1, S2 vs S2, S2 vs S3, S2 vs S4
            0.33, 1,   2,   0.25,
            // S3 vs S1, S3 vs S2, S3 vs S3, S3 vs S4
            0.2,  0.5, 1,   0.33,
            // S4 vs S1, S4 vs S2, S4 vs S3, S4 vs S4
            2,    4,   3,   1,
        ];

        foreach ($kriteria as $kri) {
            $subKriteriaIds = [];
            // Insert subkriteria untuk setiap kriteria
            foreach ($namaSubKriteria as $nama) {
                $sub = SubKriteria::create([
                    "nama" => $nama,
                    "kriteria_id" => $kri->id,
                ]);
                $subKriteriaIds[] = $sub->id;
            }

            // Isi matriks_perbandingan_subkriteria
            $j = 0;
            foreach ($subKriteriaIds as $i1) {
                foreach ($subKriteriaIds as $i2) {
                    DB::table('matriks_perbandingan_subkriteria')->insert([
                        'nilai' => $nilaiSubKriteria[$j],
                        'kriteria_id' => $kri->id,
                        'subkriteria_id' => $i1,
                        'subkriteria_id_banding' => $i2,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $j++;
                }
            }

            $this->matriks_nilai_subkriteria($kri->id, $subKriteriaIds);
            $this->matriks_penjumlahan_subkriteria($kri->id, $subKriteriaIds);
        }
    }

    public function matriks_nilai_subkriteria($kriteria_id, $subKriteriaIds)
    {
        $matriksPerbandingan = DB::table('matriks_perbandingan_subkriteria')
            ->where('kriteria_id', $kriteria_id)
            ->orderBy('subkriteria_id', 'asc')
            ->orderBy('subkriteria_id_banding', 'asc')
            ->get();

        DB::table('matriks_nilai_subkriteria')->where('kriteria_id', $kriteria_id)->delete();
        DB::table('matriks_nilai_prioritas_subkriteria')->where('kriteria_id', $kriteria_id)->delete();

        foreach ($matriksPerbandingan as $item) {
            $jumlahNilai = $matriksPerbandingan->where('subkriteria_id_banding', $item->subkriteria_id_banding)->sum('nilai');
            DB::table('matriks_nilai_subkriteria')->insert([
                'nilai' => $item->nilai / $jumlahNilai,
                'kriteria_id' => $item->kriteria_id,
                'subkriteria_id' => $item->subkriteria_id,
                'subkriteria_id_banding' => $item->subkriteria_id_banding,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $matriksNilai = DB::table('matriks_nilai_subkriteria')->where('kriteria_id', $kriteria_id)->get();

        foreach ($subKriteriaIds as $id) {
            $nilai = $matriksNilai->where('subkriteria_id', $id)->sum('nilai');
            $jumlah = count($subKriteriaIds);

            DB::table('matriks_nilai_prioritas_subkriteria')->insert([
                'prioritas' => $nilai / $jumlah,
                'kriteria_id' => $kriteria_id,
                'subkriteria_id' => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function matriks_penjumlahan_subkriteria($kriteria_id, $subKriteriaIds)
    {
        $matriksPerbandingan = DB::table('matriks_perbandingan_subkriteria')
            ->where('kriteria_id', $kriteria_id)
            ->orderBy('subkriteria_id', 'asc')
            ->orderBy('subkriteria_id_banding', 'asc')
            ->get();

        $matriksNilaiPrioritas = DB::table('matriks_nilai_prioritas_subkriteria')->where('kriteria_id', $kriteria_id)->get();

        DB::table('matriks_penjumlahan_subkriteria')->where('kriteria_id', $kriteria_id)->delete();
        DB::table('matriks_penjumlahan_prioritas_subkriteria')->where('kriteria_id', $kriteria_id)->delete();

        foreach ($matriksPerbandingan as $item) {
            $prioritas = $matriksNilaiPrioritas->where('subkriteria_id', $item->subkriteria_id_banding)->first()->prioritas;

            DB::table('matriks_penjumlahan_subkriteria')->insert([
                'nilai' => $item->nilai * $prioritas,
                'kriteria_id' => $item->kriteria_id,
                'subkriteria_id' => $item->subkriteria_id,
                'subkriteria_id_banding' => $item->subkriteria_id_banding,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $matriksPenjumlahan = DB::table('matriks_penjumlahan_subkriteria')->where('kriteria_id', $kriteria_id)->get();

        foreach ($subKriteriaIds as $id) {
            $nilai = $matriksPenjumlahan->where('subkriteria_id', $id)->sum('nilai');
            $prioritas = $matriksNilaiPrioritas->where('subkriteria_id', $id)->first()->prioritas;

            DB::table('matriks_penjumlahan_prioritas_subkriteria')->insert([
                'prioritas' => $nilai / $prioritas,
                'kriteria_id' => $kriteria_id,
                'subkriteria_id' => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
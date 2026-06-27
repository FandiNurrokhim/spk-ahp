<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;
use App\Imports\AlternatifImport;
use Maatwebsite\Excel\Facades\Excel;

class AlternatifRepository
{
    protected $alternatif, $kriteria, $penilaian;

    public function __construct(Alternatif $alternatif, Kriteria $kriteria, Penilaian $penilaian)
    {
        $this->alternatif = $alternatif;
        $this->kriteria = $kriteria;
        $this->penilaian = $penilaian;
    }

    public function getAll()
    {
        $data = $this->alternatif->orderBy('created_at', 'asc')->get();
        return $data;
    }

    public function getPaginate($perData)
    {
        $data = $this->alternatif->paginate($perData);
        return $data;
    }

    public function simpan($data)
    {
        // Simpan data alternatif
        $alternatif = $this->alternatif->create([
            'nama' => $data['nama'],
            'nik'  => $data['nik'],
        ]);

        // Simpan nilai untuk setiap kriteria
        if (isset($data['kriteria'])) {
            foreach ($data['kriteria'] as $kriteria_id => $nilai) {
                DB::table('penilaian')->updateOrInsert(
                    [
                        'alternatif_id' => $alternatif->id,
                        'kriteria_id' => $kriteria_id
                    ],
                    [
                        'nilai' => $nilai,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        // Hitung dan simpan hasil WP
        $this->hitungDanSimpanWP();

        return $alternatif;
    }

    public function import($data)
    {
        try {
            $file = $data->file('import_data');
            $import = new AlternatifImport;
            Excel::import($import, $file);

            // Jika ada baris gagal, tangani di sini (jika pakai SkipsOnFailure)
            if (method_exists($import, 'failures') && $import->failures()->isNotEmpty()) {
                $messages = $import->failures()->map(function($failure) {
                    return $failure->errors()[0] ?? 'Data tidak valid';
                })->implode(', ');
                return [
                    'success' => false,
                    'message' => $messages,
                ];
            }
    
            // Hitung dan simpan hasil WP setelah import
            $this->hitungDanSimpanWP();
            
            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function add_penilaian_alternatif()
    {
        $alternatif = $this->getAll();
        $kriteria = $this->kriteria->all();
        foreach ($alternatif as $item) {
            foreach ($kriteria as $value) {
                $penilaian = $this->penilaian->where('alternatif_id', $item->id)
                    ->where('kriteria_id', $value->id)->first();
                if ($penilaian == null) {
                    DB::table('penilaian')->insert([
                        'alternatif_id' => $item->id,
                        'kriteria_id' => $value->id,
                        'nilai' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }

    public function getDataById($id)
    {
        $data = $this->alternatif->where('id', $id)->firstOrFail();
        
        // Ambil nilai penilaian untuk setiap kriteria
        $penilaian = DB::table('penilaian')
            ->where('alternatif_id', $id)
            ->pluck('nilai', 'kriteria_id')
            ->toArray();

        return [
            'id'        => $data->id,
            'kode'      => $data->kode,
            'nama'      => $data->nama,
            'nik'       => $data->nik,
            'penilaian' => $penilaian,
        ];
    }

    public function perbarui($id, $data)
    {
        // Update data alternatif
        $this->alternatif->where('id', $id)->update([
            'nama' => $data['nama'],
            'nik'  => $data['nik'],
        ]);

        // Update nilai untuk setiap kriteria
        if (isset($data['kriteria'])) {
            foreach ($data['kriteria'] as $kriteria_id => $nilai) {
                DB::table('penilaian')->updateOrInsert(
                    [
                        'alternatif_id' => $id,
                        'kriteria_id' => $kriteria_id
                    ],
                    [
                        'nilai' => $nilai,
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        // Hitung ulang dan simpan hasil WP
        $this->hitungDanSimpanWP();

        return true;
    }

    public function hapus($id)
    {
        try {
            // Hapus penilaian terkait
            $this->penilaian->where('alternatif_id', $id)->delete();
            
            // Hapus hasil WP terkait
            DB::table('hasil_wp')->where('alternatif_id', $id)->delete();
            DB::table('detail_wp')->where('alternatif_id', $id)->delete();
            
            // Hapus alternatif
            $this->alternatif->where('id', $id)->delete();

            // Hitung ulang hasil WP untuk alternatif yang tersisa
            $this->hitungDanSimpanWP();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hitung Vektor S dan V dengan metode logaritma untuk stabilitas numerik
     */
    private function hitungDanSimpanWP()
    {
        try {
            // Hapus hasil WP yang lama
            DB::table('hasil_wp')->delete();
            DB::table('detail_wp')->delete();

            $alternatif = $this->getAll();
            $kriteria = $this->kriteria->all();

            if ($alternatif->isEmpty() || $kriteria->isEmpty()) {
                return;
            }

            // Hitung bobot relatif
            $totalBobot = $kriteria->sum('bobot');
            $bobotRelatif = [];
            foreach ($kriteria as $k) {
                $bobot = $totalBobot > 0 ? $k->bobot / $totalBobot : 0;
                // Jika cost, buat negatif
                if ($k->jenis == 'cost') {
                    $bobot = -$bobot;
                }
                $bobotRelatif[$k->id] = $bobot;
            }

            $vektorS = [];
            $totalS = 0;

            // Hitung Vektor S untuk setiap alternatif menggunakan logaritma
            foreach ($alternatif as $alt) {
                $logS = 0; // Gunakan logaritma untuk stabilitas numerik
                
                foreach ($kriteria as $k) {
                    $penilaian = DB::table('penilaian')
                        ->where('alternatif_id', $alt->id)
                        ->where('kriteria_id', $k->id)
                        ->first();
                    
                    $nilai = $penilaian ? floatval($penilaian->nilai) : 1; // Gunakan 1 jika 0 untuk menghindari log(0)
                    $pangkat = $bobotRelatif[$k->id];
                    
                    // Pastikan nilai > 0 untuk logaritma
                    if ($nilai <= 0) {
                        $nilai = 0.0001; // Nilai kecil untuk menggantikan 0
                    }
                    
                    // Gunakan logaritma: log(a^b) = b * log(a)
                    $logS += $pangkat * log($nilai);

                    // Simpan detail perhitungan
                    $hasilPow = pow($nilai, $pangkat);
                    DB::table('detail_wp')->insert([
                        'alternatif_id' => $alt->id,
                        'kriteria_id' => $k->id,
                        'nilai' => $penilaian ? $penilaian->nilai : 0,
                        'bobot' => $pangkat,
                        'hasil' => $hasilPow,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                // Konversi kembali dari logaritma ke nilai asli
                $s = exp($logS);
                $vektorS[$alt->id] = $s;
                $totalS += $s;
            }

            // Hitung Vektor V dan simpan hasil
            foreach ($alternatif as $alt) {
                $v = $totalS > 0 ? $vektorS[$alt->id] / $totalS : 0;

                DB::table('hasil_wp')->insert([
                    'alternatif_id' => $alt->id,
                    'vektor_s' => $vektorS[$alt->id],
                    'vektor_v' => $v,
                    'ranking' => 0, // Akan diupdate setelah semua data tersimpan
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Update ranking berdasarkan vektor V
            $this->updateRanking();

        } catch (\Exception $e) {
            // Log error jika diperlukan
            \Log::error('Error in hitungDanSimpanWP: ' . $e->getMessage());
        }
    }

    /**
     * Update ranking berdasarkan vektor V
     */
    private function updateRanking()
    {
        $hasil = DB::table('hasil_wp')
            ->orderBy('vektor_v', 'desc')
            ->get();

        $rank = 1;
        foreach ($hasil as $item) {
            DB::table('hasil_wp')
                ->where('id', $item->id)
                ->update(['ranking' => $rank]);
            $rank++;
        }
    }

    /**
     * Generate kode alternatif otomatis
     */
    public function generateKode()
    {
        $lastAlternatif = $this->alternatif->orderBy('id', 'desc')->first();
        if ($lastAlternatif) {
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastAlternatif->kode);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'A' . $newNumber;
    }

    /**
     * Debug: Tampilkan perhitungan manual untuk verifikasi
     */
    public function debugWP()
    {
        $alternatif = $this->getAll();
        $kriteria = $this->kriteria->all();
        
        echo "<h3>Debug Perhitungan WP</h3>";
        
        // Tampilkan data matriks
        echo "<table border='1'>";
        echo "<tr><th>Alternatif</th>";
        foreach ($kriteria as $k) {
            echo "<th>{$k->kode}</th>";
        }
        echo "</tr>";
        
        foreach ($alternatif as $alt) {
            echo "<tr><td>{$alt->kode}</td>";
            foreach ($kriteria as $k) {
                $penilaian = DB::table('penilaian')
                    ->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $k->id)
                    ->first();
                echo "<td>" . ($penilaian ? $penilaian->nilai : 0) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Tampilkan bobot
        echo "<h4>Bobot Relatif:</h4>";
        $totalBobot = $kriteria->sum('bobot');
        foreach ($kriteria as $k) {
            $bobot = $k->bobot / $totalBobot;
            if ($k->jenis == 'cost') $bobot = -$bobot;
            echo "{$k->kode}: " . number_format($bobot, 9) . "<br>";
        }
    }
}
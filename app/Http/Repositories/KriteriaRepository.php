<?php

namespace App\Http\Repositories;

use App\Imports\KriteriaImport;
use Carbon\Carbon;
use App\Models\Kriteria;
use App\Models\Alternatif;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KriteriaRepository
{
    protected $kriteria;

    public function __construct(Kriteria $kriteria)
    {
        $this->kriteria = $kriteria;
    }

    public function getAll()
    {
        $data = $this->kriteria->orderBy('created_at', 'asc')->get();
        return $data;
    }

    public function getPaginate($perData)
    {
        $data = $this->kriteria->paginate($perData);
        return $data;
    }

    public function simpan($data)
    {
        $result = $this->kriteria->create($data);
        
        // Untuk WP, hanya perlu menambah penilaian alternatif
        // Tidak perlu matriks perbandingan seperti WP
        $this->add_penilaian_alternatif();

        return $result;
    }

    public function import($data)
    {
        try {
            // menangkap file excel
            $file = $data->file('import_data');
            $import = new KriteriaImport;
            Excel::import($import, $file);

            // Jika ada baris gagal, tangani di sini (jika pakai SkipsOnFailure)
            if (method_exists($import, 'failures') && $import->failures()->isNotEmpty()) {
                $messages = $import->failures()->map(function ($failure) {
                    return $failure->errors()[0] ?? 'Data tidak valid';
                })->implode(', ');
                return [
                    'success' => false,
                    'message' => $messages,
                ];
            }

            // Untuk WP, hanya perlu menambah penilaian alternatif
            $this->add_penilaian_alternatif();

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
        $alternatif = Alternatif::all();
        $kriteria = $this->kriteria->all();
        
        foreach ($kriteria as $value) {
            foreach ($alternatif as $item) {
                $penilaian = DB::table('penilaian')
                    ->where('alternatif_id', $item->id)
                    ->where('kriteria_id', $value->id)
                    ->first();
                    
                if ($penilaian == null) {
                    DB::table('penilaian')->insert([
                        'alternatif_id' => $item->id,
                        'kriteria_id' => $value->id,
                        'nilai' => 0, // Default nilai 0 untuk WP
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }

    public function getDataById($id)
    {
        $data = $this->kriteria->where('id', $id)->firstOrFail();
        return $data;
    }

    public function perbarui($id, $data)
    {
        $result = $this->kriteria->where('id', $id)->update([
            "nama" => $data['nama'],
            "bobot" => $data['bobot'],
            "jenis" => $data['jenis'], 
        ]);
        
        // Setelah update kriteria, hapus hasil WP yang sudah ada
        // karena bobot berubah
        $this->clearWPResults();
        
        return $result;
    }

    public function hapus($id)
    {
        try {
            // Hapus data penilaian terkait
            DB::table('penilaian')
                ->where('kriteria_id', $id)
                ->delete();

            // Hapus hasil WP terkait
            DB::table('hasil_wp')->delete();
            DB::table('detail_wp')
                ->where('kriteria_id', $id)
                ->delete();

            // Setelah semua data terkait dihapus, baru hapus kriteria
            $result = $this->kriteria->where('id', $id)->delete();

            return ['success' => true, 'data' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hapus hasil perhitungan WP ketika ada perubahan kriteria
     */
    private function clearWPResults()
    {
        try {
            DB::table('hasil_wp')->delete();
            DB::table('detail_wp')->delete();
        } catch (\Exception $e) {
            // Log error jika diperlukan
        }
    }

    /**
     * Hitung bobot relatif untuk WP
     */
    public function getBobotRelatif()
    {
        $kriteria = $this->getAll();
        $totalBobot = $kriteria->sum('bobot');
        $bobotRelatif = [];

        foreach ($kriteria as $item) {
            $bobotRelatif[$item->id] = $totalBobot > 0 ? $item->bobot / $totalBobot : 0;
        }

        return $bobotRelatif;
    }

    /**
     * Generate kode kriteria otomatis
     */
    public function generateKode()
    {
        $lastKriteria = $this->kriteria->orderBy('id', 'desc')->first();
        if ($lastKriteria) {
            $lastNumber = (int) substr($lastKriteria->kode, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'C' . $newNumber;
    }
}
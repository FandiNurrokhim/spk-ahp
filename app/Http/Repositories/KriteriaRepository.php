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
        $data = $this->kriteria->create($data);
        // DB::table('matriks_perbandingan_utama')->truncate(); // Dihapus/komentari agar tidak reset
        $this->add_matriks_perbandingan_baru($data); // Tambahkan hanya perbandingan baru
        $this->add_penilaian_alternatif();

        return $data;
    }

    // Tambahkan fungsi baru untuk menambah perbandingan hanya untuk kriteria baru
    public function add_matriks_perbandingan_baru($kriteriaBaru)
    {
        $kriteriaLain = $this->kriteria->where('id', '!=', $kriteriaBaru->id)->get();

        // Perbandingan kriteria baru dengan dirinya sendiri
        DB::table('matriks_perbandingan_utama')->insert([
            'nilai' => 1,
            'kriteria_id' => $kriteriaBaru->id,
            'kriteria_id_banding' => $kriteriaBaru->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Perbandingan kriteria baru dengan kriteria lain
        foreach ($kriteriaLain as $kriteria) {
            DB::table('matriks_perbandingan_utama')->insert([
                'nilai' => null,
                'kriteria_id' => $kriteriaBaru->id,
                'kriteria_id_banding' => $kriteria->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('matriks_perbandingan_utama')->insert([
                'nilai' => null,
                'kriteria_id' => $kriteria->id,
                'kriteria_id_banding' => $kriteriaBaru->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
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
                $messages = $import->failures()->map(function($failure) {
                    return $failure->errors()[0] ?? 'Data tidak valid';
                })->implode(', ');
                return [
                    'success' => false,
                    'message' => $messages,
                ];
            }
    
            DB::table('matriks_perbandingan_utama')->truncate();
            $this->add_matriks_perbandingan();
            $this->add_penilaian_alternatif();
    
            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function add_matriks_perbandingan()
    {
        $kriteria = $this->getAll();
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
        $kriteria = $this->kriteria->all();
        foreach ($kriteria as $value) {
            foreach ($alternatif as $item) {
                $penilaian = DB::table('penilaian')->where('alternatif_id', $item->id)->where('kriteria_id', $value->id)->first();
                if ($penilaian == null) {
                    DB::table('penilaian')->insert([
                        'alternatif_id' => $item->id,
                        'kriteria_id' => $value->id,
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
        $data = $this->kriteria->where('id', $id)->update([
            "kode" => $data['kode'],
            "nama" => $data['nama'],
        ]);
        return $data;
    }

    public function hapus($id)
    {
        $data = [
            $this->kriteria->where('id', $id)->delete(),
        ];
        return $data;
    }
}

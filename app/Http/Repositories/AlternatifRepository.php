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
        $data = $this->alternatif->create($data);
        $this->add_penilaian_alternatif();
        return $data;
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
    
            $this->add_penilaian_alternatif();
            return ['success' => true];
        } catch (\Exception $e) {
            // Exception dari throw di model() akan masuk sini
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
                $penilaian = $this->penilaian->where('alternatif_id', $item->id)->where('kriteria_id', $value->id)->first();
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
        $data = $this->alternatif->where('id', $id)->firstOrFail();
        return $data;
    }

    public function perbarui($id, $data)
    {
        $data = $this->alternatif->where('id', $id)->update([
            "nama" => $data['nama'],
            'nisn' => $data['nisn'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'],
        ]);
        return $data;
    }

    public function hapus($id)
    {
        $data = [
            DB::table('hasil_solusi_ahp')->where('alternatif_id', $id)->delete(),
            $this->penilaian->where('alternatif_id', $id)->delete(),
            $this->alternatif->where('id', $id)->delete(),
        ];
        return $data;
    }
}

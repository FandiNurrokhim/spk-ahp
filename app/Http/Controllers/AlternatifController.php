<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AlternatifRequest;
use App\Http\Services\AlternatifService;
use App\Models\Kriteria;

class AlternatifController extends Controller
{
    protected $alternatifService;

    public function __construct(AlternatifService $alternatifService)
    {
        $this->alternatifService = $alternatifService;
    }

    public function index()
    {
        $judul = 'Alternatif';
        $data = $this->alternatifService->getAll();
        $kriteria = Kriteria::get();

        if ($data->last()) {
            $lastNumber = (int) substr($data->last()->kode, 1);
            $kode = "A" . ($lastNumber + 1);
        } else {
            $kode = "A1";
        }

        return view('dashboard.alternatif.index', compact('judul', 'data', 'kriteria', 'kode'));
    }

    public function simpan(AlternatifRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->alternatifService->simpanPostData($request);

            if (!$data[0]) {
                DB::rollBack();
                return redirect('dashboard/alternatif')->with('gagal', $data[1]);
            }

            DB::commit();
            return redirect('dashboard/alternatif')->with('berhasil', "Data berhasil disimpan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('dashboard/alternatif')->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function ubah(Request $request)
    {
        $data = $this->alternatifService->ubahGetData($request);
        return $data;
    }

    public function perbarui(AlternatifRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->alternatifService->perbaruiPostData($request);
            if (!$data[0]) {
                DB::rollBack();
                return redirect('dashboard/alternatif')->with('gagal', $data[1]);
            }
            DB::commit();
            return redirect('dashboard/alternatif')->with('berhasil', "Data berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('dashboard/alternatif')->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function hapus(Request $request)
    {
        $this->alternatifService->hapusPostData($request->id);
        return redirect('dashboard/alternatif');
    }

    public function import(Request $request)
    {
        // validasi
        $request->validate([
            'import_data' => 'required|mimes:xls,xlsx'
        ]);

        $result = $this->alternatifService->import($request);
        if (isset($result['success']) && !$result['success']) {
            // Hilangkan &#039; dari pesan error
            $message = str_replace("&#039;", "'", $result['message']);
            $message = str_replace("'", "", $message);
            return redirect('dashboard/alternatif')->with('gagal', $message);
        }
        // Jika sukses
        return redirect('dashboard/alternatif')->with('berhasil', "Data berhasil di import!");
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\KategoriService;
use App\Http\Services\KriteriaService;
use App\Http\Services\PenilaianService;
use App\Http\Services\SubKriteriaService;

class PenilaianController extends Controller
{
    protected $penilaianService, $kriteriaService, $subKriteriaService, $kategoriService;

    public function __construct(PenilaianService $penilaianService, KriteriaService $kriteriaService, SubKriteriaService $subKriteriaService, KategoriService $kategoriService)
    {
        $this->penilaianService = $penilaianService;
        $this->kriteriaService = $kriteriaService;
        $this->subKriteriaService = $subKriteriaService;
        $this->kategoriService = $kategoriService;
    }

    public function index()
    {
        $judul = 'Penilaian';
        $kriteria = $this->kriteriaService->getAll();

        $matriksNilaiKriteria = DB::table('matriks_nilai_prioritas_utama as mnu')
            ->join('kriteria as k', 'k.id', '=', 'mnu.kriteria_id')
            ->select('mnu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();


        $data = $this->penilaianService->getAll();
        $hasil = DB::table('hasil_solusi_ahp as hsa')
            ->join('alternatif as a', 'a.id', '=', 'hsa.alternatif_id')
            ->select('hsa.*', 'a.nama as nama_alternatif')
            ->get();

        return view('dashboard.penilaian.index', [
            'judul' => $judul,
            'data' => $data,
            'kriteria' => $kriteria,
            'matriksNilaiKriteria' => $matriksNilaiKriteria,
            'hasil' => $hasil,
        ]);
    }

    public function ubah(Request $request)
    {
        $judul = 'Penilaian Alternatif';

        $criterias = $this->kriteriaService->getAll();

        // Ambil semua data penilaian untuk alternatif ini
        $data = DB::table('penilaian')
            ->where('alternatif_id', $request->alternatif_id)
            ->get();
        // Ambil data alternatif
        $alternatif = DB::table('alternatif')->where('id', $request->alternatif_id)->first();

        return view('dashboard.penilaian.ubahPenilaianAlternatif', [
            'judul' => $judul,
            'data' => $data,
            'criterias' => $criterias,
            'alternatif' => $alternatif,
        ]);
    }

    public function perbarui(Request $request)
    {
        // Simpan/update nilai penilaian untuk setiap kriteria pada alternatif
        foreach ($request->input('nilai', []) as $kriteria_id => $nilai) {
            DB::table('penilaian')->updateOrInsert(
                [
                    'alternatif_id' => $request->alternatif_id,
                    'kriteria_id' => $kriteria_id,
                ],
                [
                    'nilai' => $nilai,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $alternatif = DB::table('alternatif')->where('id', $request->alternatif_id)->first()->nama ?? '';
        return redirect('dashboard/penilaian')->with('berhasil', ['Data Penilaian Alternatif telah diperbarui!', $alternatif]);
    }

    public function perhitungan_alternatif()
    {
        $penilaian = DB::table('penilaian')->get();
        $matriksNilaiKriteria = DB::table('matriks_nilai_prioritas_utama')->get();

        // Kosongkan tabel hasil
        DB::table('hasil_solusi_ahp')->truncate();

        foreach ($penilaian->unique('alternatif_id') as $item) {
            $nilai = 0;

            foreach ($penilaian->where('alternatif_id', $item->alternatif_id) as $value) {
                $kriteria = $matriksNilaiKriteria
                    ->where('kriteria_id', $value->kriteria_id)
                    ->first()->prioritas ?? 0;

                $nilai += $kriteria * $value->nilai;
            }

            DB::table('hasil_solusi_ahp')->insert([
                'nilai' => $nilai,
                'alternatif_id' => $item->alternatif_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('dashboard/penilaian')->with('berhasil', ['Perhitungan AHP Alternatif berhasil!', 0]);
    }
    public function hasil_akhir()
    {
        $judul = 'Hasil Akhir';
        $hasil = DB::table('hasil_solusi_ahp as hsa')
            ->join('alternatif as a', 'a.id', '=', 'hsa.alternatif_id')
            ->select('hsa.*', 'a.nama as nama_alternatif')
            ->orderBy('hsa.nilai', 'desc')
            ->get();
        return view('dashboard.penilaian.hasil', [
            'judul' => $judul,
            'hasil' => $hasil,
        ]);
    }

    public function pdf_ahp()
    {
        $judul = 'Laporan Hasil AHP';
        $kriteria = $this->kriteriaService->getAll();
    
        $matriksNilaiKriteria = DB::table('matriks_nilai_prioritas_utama as mnu')
            ->join('kriteria as k', 'k.id', '=', 'mnu.kriteria_id')
            ->select('mnu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();
    
        if ($matriksNilaiKriteria->where('kriteria_id', $kriteria->last()->id)->first() == null) {
            return redirect('dashboard/kriteria/perhitungan_utama')->with('gagal', 'Perhitungan Kriteria Utama belum tuntas!');
        }
    
        $data = $this->penilaianService->getAll();
        $hasil = DB::table('hasil_solusi_ahp as hsa')
            ->join('alternatif as a', 'a.id', '=', 'hsa.alternatif_id')
            ->select('hsa.*', 'a.nama as nama_alternatif')
            ->get();
    
        $pdf = PDF::setOptions(['defaultFont' => 'sans-serif'])->loadview('dashboard.pdf.penilaian', [
            'judul' => $judul,
            'data' => $data,
            'kriteria' => $kriteria,
            'matriksNilaiKriteria' => $matriksNilaiKriteria,
            'hasil' => $hasil,
        ]);
    
        // return $pdf->download('laporan-penilaian.pdf');
        return $pdf->stream();
    }

    public function pdf_hasil()
    {
        $judul = 'Laporan Hasil Akhir';
        $hasil = DB::table('hasil_solusi_ahp as hsa')
            ->join('alternatif as a', 'a.id', '=', 'hsa.alternatif_id')
            ->select('hsa.*', 'a.nama as nama_alternatif')
            ->orderBy('hsa.nilai', 'desc')
            ->get();

        $pdf = PDF::setOptions(['defaultFont' => 'sans-serif'])->loadview('dashboard.pdf.hasil_akhir', [
            'judul' => $judul,
            'hasil' => $hasil,
        ]);

        // return $pdf->download('laporan-penilaian.pdf');
        return $pdf->stream();
    }
}

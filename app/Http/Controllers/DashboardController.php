<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kategori;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $judul = 'Dashboard';

        $kriteria = Kriteria::get();
        $kategori = Kategori::get()->count();
        $alternatif = Alternatif::get();

        $hasilSolusi = DB::table('hasil_solusi_ahp as hsa')
            ->join('alternatif as a', 'a.id', '=', 'hsa.alternatif_id')
            ->select('hsa.*', 'a.nama as nama_alternatif')
            ->get();

        $hasilNilaiData = '';
        foreach ($hasilSolusi as $item) {
            $hasilNilaiData .= number_format($item->nilai, 3) . ", ";
        }
        $hasilNilaiData = rtrim($hasilNilaiData, ", ");

        return view('dashboard.index', compact('judul', 'kriteria', 'kategori', 'alternatif', 'hasilSolusi', 'hasilNilaiData'));
    }

    public function profile(Request $request)
    {
        $judul = 'Profile';
        return view('dashboard.profile', [
            'judul' => $judul,
            'user' => auth()->user(),
        ]);
    }

    public function indexReset()
    {
        $judul = 'Reset Data';
        return view('dashboard.reset.index', compact('judul'));
    }

    public function resetAlternatif()
    {
        // Hapus data relasi dulu
        DB::table('penilaian')->delete();
        DB::table('hasil_solusi_ahp')->delete();
        // Baru hapus data alternatif
        DB::table('alternatif')->delete();

        return redirect()->route('reset')->with('success', 'Data alternatif berhasil direset.');
    }


    public function resetAll()
    {
        // Matikan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('alternatif')->truncate();
        DB::table('kriteria')->truncate();
        DB::table('hasil_solusi_ahp')->truncate();
        DB::table('penilaian')->truncate();
        DB::table('matriks_perbandingan_utama')->truncate();
        DB::table('matriks_nilai_utama')->truncate();
        DB::table('matriks_penjumlahan_utama')->truncate();
        DB::table('matriks_penjumlahan_prioritas_utama')->truncate();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('reset')->with('success', 'Semua data berhasil direset.');
    }
}

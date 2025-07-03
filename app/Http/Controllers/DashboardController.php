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
        $alternatif = Alternatif::get();

        // Ganti dari hasil_solusi_ahp ke hasil_wp dengan kolom yang benar
        $hasilSolusi = DB::table('hasil_wp as hw')
            ->join('alternatif as a', 'a.id', '=', 'hw.alternatif_id')
            ->select('hw.*', 'a.nama as nama_alternatif', 'a.kode')
            ->orderBy('hw.ranking', 'asc')
            ->get();

        $hasilNilaiData = '';
        foreach ($hasilSolusi as $item) {
            // Ganti dari nilai_v ke vektor_v
            $hasilNilaiData .= number_format($item->vektor_v, 6) . ", ";
        }
        $hasilNilaiData = rtrim($hasilNilaiData, ", ");

        return view('dashboard.index', compact('judul', 'kriteria', 'alternatif', 'hasilSolusi', 'hasilNilaiData'));
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
        DB::table('alternatif')->delete();
        DB::table('hasil_wp')->delete();
        DB::table('detail_wp')->delete();

        return redirect()->back()->with('success', 'Data alternatif berhasil direset.');
    }


    public function resetAll()
    {
        // Matikan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('alternatif')->truncate();
        DB::table('kriteria')->truncate();
        DB::table('hasil_wp')->truncate();
        DB::table('detail_wp')->truncate();
        DB::table('penilaian')->truncate();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->back()->with('success', 'Semua data berhasil direset.');
    }
}

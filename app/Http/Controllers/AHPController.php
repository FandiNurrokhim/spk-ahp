<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\KategoriService;
use App\Http\Services\KriteriaService;
use App\Http\Services\SubKriteriaService;

class AHPController extends Controller
{
    protected $kriteriaService, $kategoriService, $subKriteriaService;

    public function __construct(KriteriaService $kriteriaService, SubKriteriaService $subKriteriaService)
    {
        $this->kriteriaService = $kriteriaService;
        $this->subKriteriaService = $subKriteriaService;
    }

    public function index_perhitungan_utama()
    {
        if ($this->kriteriaService->getAll()->count() < 3) {
            return redirect('dashboard/kriteria')->with('gagal', "Kriteria harus lebih dari sama dengan 3!");
        }

        $judul = 'Perbandingan Kriteria';

        $kriteria = $this->kriteriaService->getAll();
        $matriksPerbandingan = DB::table('matriks_perbandingan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        $matriksNilai = DB::table('matriks_nilai_utama as mnu')
            ->join('kriteria as k', 'k.id', '=', 'mnu.kriteria_id')
            ->select('mnu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        $matriksPenjumlahan = DB::table('matriks_penjumlahan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        $matriksPenjumlahanPrioritas = DB::table('matriks_penjumlahan_prioritas_utama')->get();
        $IR = DB::table('index_random_consistency')->where('ukuran_matriks', $kriteria->count())->first()->nilai;

        // dd($matriksNilai->where('kriteria_id', $kriteria->last()->id)->first());

        return view('dashboard.perhitungan_utama.index', [
            'judul' => $judul,
            'kriteria' => $kriteria,
            'matriksPerbandingan' => $matriksPerbandingan,
            'matriksNilai' => $matriksNilai,
            'matriksPenjumlahan' => $matriksPenjumlahan,
            'matriksPenjumlahanPrioritas' => $matriksPenjumlahanPrioritas,
            'IR' => $IR,
        ]);
    }

    public function ubah_matriks_perbandingan_utama(Request $request)
    {
        $namaKriteria = $this->kriteriaService->getDataById($request->kriteria_id);
        $judul = 'Matriks Perbandingan Utama:';

        $matriksPerbandingan = DB::table('matriks_perbandingan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->where('mpu.kriteria_id', '=', $request->kriteria_id)
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->get();

        foreach ($this->kriteriaService->getAll() as $value => $item) {
            if ($matriksPerbandingan[$value]->kriteria_id_banding == $item->id) {
                $matriksPerbandingan[$value]->nama_kriteria_banding = $item->nama;
            }
        }

        // dd($matriksPerbandingan);

        return view('dashboard.perhitungan_utama.ubahMatriksPerbandingan', [
            'judul' => $judul,
            'namaKriteria' => $namaKriteria,
            'matriksPerbandingan' => $matriksPerbandingan,
        ]);
    }

    public function matriks_utama()
    {
        $this->matriks_nilai_utama();
        $this->matriks_penjumlahan_utama();

        return redirect('dashboard/kriteria/perhitungan_utama')->with('berhasil', ["Perhitungan matriks utama berhasil!", 0]);
    }

    public function matriks_perbandingan_utama(Request $request)
    {

        foreach ($this->kriteriaService->getAll() as $value => $item) {
            $nilai = $request->post()[$item->id];

            // Update nilai utama
            DB::table('matriks_perbandingan_utama')
                ->where('kriteria_id', $request->kriteria_id)
                ->where('kriteria_id_banding', $item->id)
                ->update([
                    'nilai' => $nilai,
                ]);

            // Update nilai resiprokalnya
            DB::table('matriks_perbandingan_utama')
                ->where('kriteria_id', $item->id)
                ->where('kriteria_id_banding', $request->kriteria_id)
                ->update([
                    'nilai' => 1 / $nilai,
                ]);
        }

        return redirect('dashboard/kriteria/perhitungan_utama')->with('berhasil', ["Matriks Perbandingan berhasil ditambahkan!", $this->kriteriaService->getDataById($request->kriteria_id)->nama]);
    }

    public function matriks_nilai_utama()
    {
        $matriksPerbandingan = DB::table('matriks_perbandingan_utama as mpu')
            ->join('kriteria as k', 'k.id', '=', 'mpu.kriteria_id')
            ->select('mpu.*', 'k.id as kriteria_id', 'k.nama as nama_kriteria')
            ->orderBy('mpu.kriteria_id', 'asc')
            ->orderBy('mpu.kriteria_id_banding', 'asc')
            ->get();

        $kriteria = $this->kriteriaService->getAll();

        // $dataNilai = [];
        DB::table('matriks_nilai_utama')->truncate();
        DB::table('matriks_nilai_prioritas_utama')->truncate();
        foreach ($matriksPerbandingan as $item) {
            $jumlahNilai = $matriksPerbandingan->where('kriteria_id_banding', $item->kriteria_id_banding)->sum('nilai');

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

        $kriteria = $this->kriteriaService->getAll();

        DB::table('matriks_penjumlahan_utama')->truncate();
        DB::table('matriks_penjumlahan_prioritas_utama')->truncate();
        foreach ($matriksPerbandingan as $item) {
            $prioritas = $matriksNilaiPrioritas->where('kriteria_id', $item->kriteria_id_banding)->first()->prioritas;

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


            DB::table('matriks_penjumlahan_prioritas_utama')->insert([
                'prioritas' => $nilai / $prioritas,
                'kriteria_id' => $item->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

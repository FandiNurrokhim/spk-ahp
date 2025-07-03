<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\KriteriaService;
use App\Models\Alternatif;
use App\Models\Kriteria;

class WPController extends Controller
{
    protected $kriteriaService;

    public function __construct(KriteriaService $kriteriaService)
    {
        $this->kriteriaService = $kriteriaService;
    }

    // Method utama untuk WP
    public function index()
    {
        if ($this->kriteriaService->getAll()->count() < 2) {
            return redirect('dashboard/kriteria')->with('gagal', "Kriteria harus lebih dari sama dengan 2!");
        }

        $judul = 'Perhitungan Weighted Product';

        $kriteria = $this->kriteriaService->getAll();
        $alternatif = Alternatif::orderBy('kode', 'asc')->get();

        // Pastikan ada data alternatif
        if ($alternatif->isEmpty()) {
            return redirect('dashboard/alternatif')->with('gagal', "Belum ada data alternatif!");
        }

        // Hitung bobot relatif (wj)
        $bobotRelatif = $this->hitungBobotRelatif($kriteria);

        // Hitung pangkat (untuk cost = negatif)
        $pangkat = $this->hitungPangkat($kriteria, $bobotRelatif);

        // Ambil data matriks alternatif vs kriteria
        $matriks = $this->getMatriksAlternatifKriteria($alternatif, $kriteria);

        // Hitung nilai S (vektor S)
        $nilaiS = $this->hitungNilaiS($alternatif, $kriteria, $pangkat);

        // Hitung nilai V (preferensi relatif)
        $nilaiV = $this->hitungNilaiV($nilaiS);

        // Ranking
        $ranking = $this->hitungRanking($nilaiV);

        return view('dashboard.penilaian.hasil', [
            'judul' => $judul,
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'bobotRelatif' => $bobotRelatif,
            'pangkat' => $pangkat,
            'matriks' => $matriks,
            'nilaiS' => $nilaiS,
            'nilaiV' => $nilaiV,
            'ranking' => $ranking,
        ]);
    }

    // Hitung bobot relatif (wj)
    private function hitungBobotRelatif($kriteria)
    {
        $totalBobot = $kriteria->sum('bobot');
        $bobotRelatif = [];

        foreach ($kriteria as $k) {
            $bobotRelatif[$k->id] = $totalBobot > 0 ? round($k->bobot / $totalBobot, 9) : 0;
        }

        return $bobotRelatif;
    }

    // Hitung pangkat (cost = negatif)
    private function hitungPangkat($kriteria, $bobotRelatif)
    {
        $pangkat = [];

        foreach ($kriteria as $k) {
            $bobot = $bobotRelatif[$k->id];

            if ($k->jenis == 'cost') {
                $bobot = -$bobot;
            }

            $pangkat[$k->id] = $bobot;
        }

        return $pangkat;
    }

    // Get matriks alternatif vs kriteria
    private function getMatriksAlternatifKriteria($alternatif, $kriteria)
    {
        $matriks = [];

        foreach ($alternatif as $alt) {
            $matriks[$alt->id] = [];

            foreach ($kriteria as $krit) {
                $penilaian = DB::table('penilaian')
                    ->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();

                $matriks[$alt->id][$krit->id] = $penilaian ? floatval($penilaian->nilai) : 0;
            }
        }

        return $matriks;
    }

    // Hitung Vektor S
    private function hitungNilaiS($alternatif, $kriteria, $pangkat)
    {
        $nilaiS = [];

        foreach ($alternatif as $alt) {
            $s = 1;

            foreach ($kriteria as $krit) {
                // Ambil nilai alternatif untuk kriteria ini
                $penilaian = DB::table('penilaian')
                    ->where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();

                if ($penilaian && $penilaian->nilai > 0) {
                    $nilai = floatval($penilaian->nilai);
                    $bobotPangkat = $pangkat[$krit->id];

                    // S_i = S_i * (X_ij^wj)
                    $s *= pow($nilai, $bobotPangkat);
                }
            }

            $nilaiS[$alt->id] = $s;
        }

        return $nilaiS;
    }

    // Hitung Vektor V (preferensi relatif)
    private function hitungNilaiV($nilaiS)
    {
        $totalS = array_sum($nilaiS);
        $nilaiV = [];

        foreach ($nilaiS as $altId => $s) {
            $nilaiV[$altId] = $totalS > 0 ? round($s / $totalS, 9) : 0;
        }

        return $nilaiV;
    }

    // Hitung ranking berdasarkan nilai V
    private function hitungRanking($nilaiV)
    {
        // Urutkan nilai V dari tertinggi ke terendah
        arsort($nilaiV);

        $ranking = [];
        $rank = 1;

        foreach ($nilaiV as $altId => $v) {
            $ranking[$altId] = $rank++;
        }

        return $ranking;
    }

    // Simpan hasil WP ke database
    public function simpanHasilWP()
    {
        try {
            $kriteria = $this->kriteriaService->getAll();
            $alternatif = Alternatif::all();

            if ($kriteria->isEmpty() || $alternatif->isEmpty()) {
                return redirect()->back()->with('gagal', 'Data kriteria atau alternatif tidak tersedia!');
            }

            // Hitung semua nilai
            $bobotRelatif = $this->hitungBobotRelatif($kriteria);
            $pangkat = $this->hitungPangkat($kriteria, $bobotRelatif);
            $nilaiS = $this->hitungNilaiS($alternatif, $kriteria, $pangkat);
            $nilaiV = $this->hitungNilaiV($nilaiS);
            $ranking = $this->hitungRanking($nilaiV);

            // Hapus hasil sebelumnya
            DB::table('hasil_wp')->delete();
            DB::table('detail_wp')->delete();

            // Simpan hasil ke database
            foreach ($alternatif as $alt) {
                DB::table('hasil_wp')->insert([
                    'alternatif_id' => $alt->id,
                    'vektor_s' => $nilaiS[$alt->id],    // Ganti dari 'nilai_s'
                    'vektor_v' => $nilaiV[$alt->id],    // Ganti dari 'nilai_v'
                    'ranking' => $ranking[$alt->id],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Simpan detail perhitungan per kriteria
                foreach ($kriteria as $krit) {
                    $penilaian = DB::table('penilaian')
                        ->where('alternatif_id', $alt->id)
                        ->where('kriteria_id', $krit->id)
                        ->first();

                    $nilai = $penilaian ? floatval($penilaian->nilai) : 0;
                    $bobotPangkat = $pangkat[$krit->id];
                    $hasil = $nilai > 0 ? pow($nilai, $bobotPangkat) : 0;

                    DB::table('detail_wp')->insert([
                        'alternatif_id' => $alt->id,
                        'kriteria_id' => $krit->id,
                        'nilai' => $nilai,               // Kolom ini harus ada di tabel
                        'bobot' => $bobotPangkat,        // Kolom ini harus ada di tabel
                        'hasil' => $hasil,               // Kolom ini harus ada di tabel
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            return redirect('dashboard/kriteria/hasil')->with('berhasil', 'Perhitungan Weighted Product berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk refresh/hitung ulang
    public function hitungUlang()
    {
        return $this->simpanHasilWP();
    }

    // Method untuk export hasil (opsional)
    public function exportHasil()
    {
        try {
            $hasil = DB::table('hasil_wp')
                ->join('alternatif', 'hasil_wp.alternatif_id', '=', 'alternatif.id')
                ->select('alternatif.kode', 'alternatif.nama', 'hasil_wp.*')
                ->orderBy('ranking', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $hasil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Debug method untuk troubleshooting
    public function debug()
    {
        $kriteria = $this->kriteriaService->getAll();
        $alternatif = Alternatif::all();

        $bobotRelatif = $this->hitungBobotRelatif($kriteria);
        $pangkat = $this->hitungPangkat($kriteria, $bobotRelatif);
        $matriks = $this->getMatriksAlternatifKriteria($alternatif, $kriteria);

        return response()->json([
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'bobot_relatif' => $bobotRelatif,
            'pangkat' => $pangkat,
            'matriks' => $matriks,
        ]);
    }


    public function pdf_hasil()
    {
        try {
            // Ambil data hasil dari database
            $hasil = DB::table('hasil_wp')
                ->join('alternatif', 'hasil_wp.alternatif_id', '=', 'alternatif.id')
                ->select(
                    'alternatif.id as alternatif_id',
                    'alternatif.kode',
                    'alternatif.nama as nama_alternatif',
                    'hasil_wp.vektor_s',
                    'hasil_wp.vektor_v as nilai',
                    'hasil_wp.ranking'
                )
                ->orderBy('hasil_wp.ranking', 'asc')
                ->get();

            // Cek apakah ada data hasil
            if ($hasil->isEmpty()) {
                return redirect()->back()->with('gagal', 'Belum ada hasil perhitungan WP! Silakan hitung terlebih dahulu.');
            }

            // Ambil data kriteria
            $kriteria = $this->kriteriaService->getAll();

            // Ambil data penilaian matrix
            $penilaianMatrix = [];
            foreach ($hasil as $item) {
                $penilaianMatrix[$item->nama_alternatif] = [];
                foreach ($kriteria as $k) {
                    $penilaian = DB::table('penilaian')
                        ->where('alternatif_id', $item->alternatif_id)
                        ->where('kriteria_id', $k->id)
                        ->first();

                    $penilaianMatrix[$item->nama_alternatif][$k->id] = $penilaian ? $penilaian->nilai : 0;
                }
            }

            // Ambil data vector S dari database
            $vectorS = collect();
            foreach ($hasil as $item) {
                $vectorS[$item->nama_alternatif] = $item->vektor_s;
            }

            // Data untuk PDF
            $data = [
                'judul' => 'Laporan Hasil Perhitungan Weighted Product (WP)',
                'hasil' => $hasil,
                'kriteria' => $kriteria,
                'penilaianMatrix' => $penilaianMatrix,
                'vectorS' => $vectorS,
                'tanggal' => Carbon::now()->format('d F Y'),
                'waktu' => Carbon::now()->format('H:i:s')
            ];

            // Generate PDF
            $pdf = \PDF::loadView('dashboard.pdf.hasil_akhir', $data);

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'portrait');

            // Set options untuk better rendering
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

            // Download PDF dengan nama file yang descriptive
            $filename = 'Laporan_Hasil_WP_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back()->with('gagal', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Imports;

use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AlternatifImport implements ToCollection, WithHeadingRow
{
    protected $kriteria;
    protected $errors = [];
    protected $requiredHeaders = ['kode', 'nama', 'nik'];

    public function __construct()
    {
        $this->kriteria = Kriteria::orderBy('kode', 'asc')->get();
        
        // Tambahkan header kriteria yang diperlukan
        foreach ($this->kriteria as $k) {
            $this->requiredHeaders[] = strtolower($k->kode); // c1, c2, c3, dst
        }
    }

    public function collection(Collection $rows)
    {
        // Validasi header terlebih dahulu
        if (!$this->validateHeaders($rows)) {
            throw new \Exception(implode(' | ', $this->errors));
        }

        // Log untuk debug
        \Log::info("AlternatifImport: Starting import process with " . $rows->count() . " rows");

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header

            // Log row data untuk debug
            \Log::info("Processing row {$rowNumber}: ", $row->toArray());

            // Skip baris kosong atau keterangan
            if (empty($row['kode']) || empty($row['nama']) || empty($row['nik'])) {
                \Log::info("Skipping row {$rowNumber}: empty kode or nama");
                continue;
            }

            // Skip baris yang berisi keterangan
            if (strpos(strtolower($row['nama']), 'keterangan') !== false ||
                strpos(strtolower($row['nama']), 'field') !== false) {
                \Log::info("Skipping row {$rowNumber}: contains explanation text");
                continue;
            }

            // Validasi row
            if (!$this->validateRow($row, $rowNumber)) {
                \Log::error("Row {$rowNumber} failed validation");
                continue;
            }

            try {
                // Disable auto reorder untuk performa
                config(['app.disable_alternatif_reorder' => true]);

                // Cek apakah alternatif sudah ada berdasarkan nama
                $existingAlternatif = Alternatif::where('nama', $row['nama'])->first();
                
                if ($existingAlternatif) {
                    // Update data yang sudah ada
                    $alternatif = $existingAlternatif;
                    $alternatif->update([
                        'nama'       => $row['nama'],
                        'nik'        => (string) $row['nik'],
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    \Log::info("Alternatif updated: ID {$alternatif->id}, Kode: {$alternatif->kode}, Nama: {$alternatif->nama}");
                } else {
                    // Simpan data baru
                    $alternatif = Alternatif::create([
                        'kode'       => 'TEMP_' . uniqid(),
                        'nama'       => $row['nama'],
                        'nik'        => (string) $row['nik'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    \Log::info("Alternatif created: ID {$alternatif->id}, Kode: {$alternatif->kode}, Nama: {$alternatif->nama}");
                }

                // Simpan nilai untuk setiap kriteria
                foreach ($this->kriteria as $kriteria) {
                    $kodeKriteria = strtolower($kriteria->kode); // c1, c2, c3, dst
                    
                    if (isset($row[$kodeKriteria]) && $row[$kodeKriteria] !== null && $row[$kodeKriteria] !== '') {
                        $nilai = floatval($row[$kodeKriteria]);
                        
                        $penilaian = DB::table('penilaian')->updateOrInsert(
                            [
                                'alternatif_id' => $alternatif->id,
                                'kriteria_id' => $kriteria->id
                            ],
                            [
                                'nilai' => $nilai,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );
                        
                        \Log::info("Penilaian saved: Alternatif {$alternatif->id}, Kriteria {$kriteria->id}, Nilai {$nilai}");
                    } else {
                        \Log::warning("Missing or empty value for kriteria {$kriteria->kode} in row {$rowNumber}");
                    }
                }

            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: Error menyimpan data - " . $e->getMessage();
                \Log::error("Error saving alternatif row {$rowNumber}: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
            }
        }

        // Re-enable auto reorder dan lakukan reorder sekali saja di akhir
        config(['app.disable_alternatif_reorder' => false]);
        
        // Reorder kodes
        try {
            Alternatif::reorderKodes();
            \Log::info("Alternatif codes reordered successfully");
        } catch (\Exception $e) {
            \Log::error("Error reordering alternatif codes: " . $e->getMessage());
        }

        // Throw exception jika ada error
        if (!empty($this->errors)) {
            throw new \Exception(implode(' | ', $this->errors));
        }

        \Log::info("AlternatifImport: Import process completed successfully");
    }

    /**
     * Validasi header file Excel
     */
    private function validateHeaders($rows): bool
    {
        if ($rows->isEmpty()) {
            $this->errors[] = "File Excel kosong atau tidak memiliki data";
            return false;
        }

        // Ambil row pertama untuk cek header
        $firstRow = $rows->first();
        if (!$firstRow) {
            $this->errors[] = "Tidak dapat membaca header file Excel";
            return false;
        }

        // Ambil semua key dari row pertama (header)
        $actualHeaders = array_keys($firstRow->toArray());
        
        \Log::info("Excel headers found: ", $actualHeaders);
        \Log::info("Required headers: ", $this->requiredHeaders);

        // Cek apakah semua header yang diperlukan ada
        $missingHeaders = [];
        foreach ($this->requiredHeaders as $requiredHeader) {
            if (!in_array($requiredHeader, $actualHeaders)) {
                $missingHeaders[] = $requiredHeader;
            }
        }

        if (!empty($missingHeaders)) {
            $this->errors[] = "Header yang diperlukan tidak ditemukan: " . implode(', ', $missingHeaders) . 
                             ". Header yang ditemukan: " . implode(', ', $actualHeaders) . 
                             ". Pastikan menggunakan template alternatif yang benar.";
            return false;
        }

        // Cek apakah ada header yang tidak seharusnya (misal dari template kriteria)
        $forbiddenHeaders = ['bobot', 'jenis'];
        $foundForbiddenHeaders = [];
        foreach ($forbiddenHeaders as $forbiddenHeader) {
            if (in_array($forbiddenHeader, $actualHeaders)) {
                $foundForbiddenHeaders[] = $forbiddenHeader;
            }
        }

        if (!empty($foundForbiddenHeaders)) {
            $this->errors[] = "File ini sepertinya template kriteria (ditemukan header: " . 
                             implode(', ', $foundForbiddenHeaders) . 
                             "). Silakan gunakan template alternatif yang memiliki header: " . 
                             implode(', ', $this->requiredHeaders);
            return false;
        }

        // Cek apakah kriteria kosong
        if ($this->kriteria->isEmpty()) {
            $this->errors[] = "Belum ada data kriteria. Silakan tambahkan kriteria terlebih dahulu sebelum import alternatif.";
            return false;
        }

        return true;
    }

    private function validateRow($row, $rowNumber): bool
    {
        $valid = true;

        // Validasi kode (optional untuk validasi, karena akan di-generate ulang)
        if (empty($row['kode'])) {
            $this->errors[] = "Baris {$rowNumber}: Kode alternatif wajib diisi";
            $valid = false;
        }

        // Validasi nama
        if (empty($row['nama'])) {
            $this->errors[] = "Baris {$rowNumber}: Nama alternatif wajib diisi";
            $valid = false;
        } elseif (strlen($row['nama']) > 255) {
            $this->errors[] = "Baris {$rowNumber}: Nama alternatif maksimal 255 karakter";
            $valid = false;
        }

        // Validasi NIK
        if (empty($row['nik'])) {
            $this->errors[] = "Baris {$rowNumber}: NIK wajib diisi";
            $valid = false;
        } elseif (strlen((string) $row['nik']) !== 16) {
            $this->errors[] = "Baris {$rowNumber}: NIK harus 16 digit";
            $valid = false;
        } elseif (!ctype_digit((string) $row['nik'])) {
            $this->errors[] = "Baris {$rowNumber}: NIK hanya boleh berisi angka";
            $valid = false;
        }

        // Validasi nilai kriteria
        foreach ($this->kriteria as $kriteria) {
            $kodeKriteria = strtolower($kriteria->kode); // c1, c2, c3, dst
            
            if (!isset($row[$kodeKriteria]) || $row[$kodeKriteria] === null || $row[$kodeKriteria] === '') {
                $this->errors[] = "Baris {$rowNumber}: Nilai {$kriteria->kode} wajib diisi";
                $valid = false;
            } elseif (!is_numeric($row[$kodeKriteria])) {
                $this->errors[] = "Baris {$rowNumber}: Nilai {$kriteria->kode} harus berupa angka";
                $valid = false;
            } elseif (floatval($row[$kodeKriteria]) < 0) {
                $this->errors[] = "Baris {$rowNumber}: Nilai {$kriteria->kode} harus lebih besar atau sama dengan 0";
                $valid = false;
            }
        }

        return $valid;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
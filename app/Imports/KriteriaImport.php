<?php

namespace App\Imports;

use App\Models\Kriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class KriteriaImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $requiredHeaders = ['nama', 'bobot', 'jenis'];

    public function collection(Collection $rows)
    {
        // Validasi header terlebih dahulu
        if (!$this->validateHeaders($rows)) {
            throw new \Exception(implode(' | ', $this->errors));
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header

            // Skip baris kosong atau keterangan
            if (empty($row['nama']) || empty($row['bobot']) || empty($row['jenis'])) {
                continue;
            }

            // Skip baris yang berisi keterangan
            if (strpos(strtolower($row['nama']), 'keterangan') !== false ||
                strpos(strtolower($row['nama']), 'field') !== false ||
                strpos(strtolower($row['nama']), 'jenis kriteria') !== false) {
                continue;
            }

            // Validasi row
            if (!$this->validateRow($row, $rowNumber)) {
                continue;
            }

            try {
                // Cek apakah kriteria sudah ada
                $existingKriteria = Kriteria::where('nama', $row['nama'])->first();
                
                if ($existingKriteria) {
                    // Update data yang sudah ada (tanpa mengubah kode)
                    $existingKriteria->update([
                        'bobot' => floatval($row['bobot']),
                        'jenis' => strtolower($row['jenis']),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    \Log::info("Kriteria updated: ID {$existingKriteria->id}, Kode: {$existingKriteria->kode}, Nama: {$existingKriteria->nama}");
                } else {
                    // Simpan data baru (kode akan di-generate otomatis oleh booted event)
                    $kriteria = Kriteria::create([
                        'kode' => 'TEMP', // Temporary kode, akan di-update oleh booted event
                        'nama' => $row['nama'],
                        'bobot' => floatval($row['bobot']),
                        'jenis' => strtolower($row['jenis']),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    \Log::info("Kriteria created: ID {$kriteria->id}, Kode: {$kriteria->kode}, Nama: {$kriteria->nama}");
                }

            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: Error menyimpan data - " . $e->getMessage();
                \Log::error("Error saving kriteria row {$rowNumber}: " . $e->getMessage());
            }
        }

        // Throw exception jika ada error
        if (!empty($this->errors)) {
            throw new \Exception(implode(' | ', $this->errors));
        }
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
                             ". Pastikan menggunakan template kriteria yang benar.";
            return false;
        }

        // Cek apakah ada header yang tidak seharusnya (misal dari template alternatif)
        $forbiddenHeaders = ['kode', 'c1', 'c2', 'c3', 'c4', 'c5'];
        $foundForbiddenHeaders = [];
        foreach ($forbiddenHeaders as $forbiddenHeader) {
            if (in_array($forbiddenHeader, $actualHeaders)) {
                $foundForbiddenHeaders[] = $forbiddenHeader;
            }
        }

        if (!empty($foundForbiddenHeaders)) {
            $this->errors[] = "File ini sepertinya template alternatif (ditemukan header: " . 
                             implode(', ', $foundForbiddenHeaders) . 
                             "). Silakan gunakan template kriteria yang memiliki header: " . 
                             implode(', ', $this->requiredHeaders);
            return false;
        }

        return true;
    }

    private function validateRow($row, $rowNumber): bool
    {
        $valid = true;

        // Validasi nama
        if (empty($row['nama'])) {
            $this->errors[] = "Baris {$rowNumber}: Nama kriteria wajib diisi";
            $valid = false;
        } elseif (strlen($row['nama']) > 255) {
            $this->errors[] = "Baris {$rowNumber}: Nama kriteria maksimal 255 karakter";
            $valid = false;
        }

        // Validasi bobot
        if (empty($row['bobot'])) {
            $this->errors[] = "Baris {$rowNumber}: Bobot kriteria wajib diisi";
            $valid = false;
        } elseif (!is_numeric($row['bobot'])) {
            $this->errors[] = "Baris {$rowNumber}: Bobot kriteria harus berupa angka";
            $valid = false;
        } elseif (floatval($row['bobot']) <= 0) {
            $this->errors[] = "Baris {$rowNumber}: Bobot kriteria harus lebih besar dari 0";
            $valid = false;
        }

        // Validasi jenis
        if (empty($row['jenis'])) {
            $this->errors[] = "Baris {$rowNumber}: Jenis kriteria wajib diisi";
            $valid = false;
        } elseif (!in_array(strtolower($row['jenis']), ['cost', 'benefit'])) {
            $this->errors[] = "Baris {$rowNumber}: Jenis kriteria harus 'cost' atau 'benefit'";
            $valid = false;
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
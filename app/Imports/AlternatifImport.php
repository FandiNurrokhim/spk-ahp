<?php

namespace App\Imports;

use App\Models\Alternatif;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AlternatifImport implements ToModel, WithStartRow, WithHeadingRow
{
    use Importable, SkipsFailures;

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        $required = ['alternatif', 'nisn', 'jenis_kelamin', 'tanggal_lahir', 'alamat'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $row) || is_null($row[$field])) {
                throw new \Exception("Kolom '$field' tidak ditemukan atau kosong di file Excel.");
            }
        }

        // Deteksi jika tanggal_lahir berupa angka (Excel serial date)
        $tanggal = $row['tanggal_lahir'];
        if (is_numeric($tanggal)) {
            $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
        } else {
            $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        }

        return new Alternatif([
            'nama' => $row['alternatif'],
            'nisn' => $row['nisn'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'tanggal_lahir' => $tanggal,
            'alamat' => $row['alamat'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

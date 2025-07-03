<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Kriteria;

class TemplateController extends Controller
{
    public function downloadTemplateAlternatif()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul sheet
        $sheet->setTitle('Template Alternatif');

        // Header
        $headers = ['kode', 'nama'];

        // Ambil kriteria dari database
        $kriteria = Kriteria::orderBy('kode', 'asc')->get();
        foreach ($kriteria as $k) {
            $headers[] = $k->kode;
        }

        // Set header di row 1
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Style header
        $headerRange = 'A1:' . chr(64 + count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Data contoh
        $contohData = [
            ['A1', 'Contoh Nama 1', 500, 70, 10, 80, 3000],
            ['A2', 'Contoh Nama 2', 300, 90, 10, 60, 2500],
            ['A3', 'Contoh Nama 3', 400, 80, 9, 90, 2000]
        ];

        // Input data contoh
        $row = 2;
        foreach ($contohData as $data) {
            $col = 'A';
            foreach ($data as $index => $value) {
                if ($index < count($headers)) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
            }
            $row++;
        }

        // Keterangan
        $keteranganRow = $row + 1;
        $sheet->mergeCells('A' . $keteranganRow . ':' . chr(64 + count($headers)) . ($keteranganRow + 6));

        $keterangan = "KETERANGAN:\n";
        $keterangan .= "- kode: Kode alternatif (A1, A2, dst)\n";
        $keterangan .= "- nama: Nama alternatif\n";

        foreach ($kriteria as $k) {
            $keterangan .= "- {$k->kode}: {$k->nama} ({$k->jenis})\n";
        }

        $sheet->setCellValue('A' . $keteranganRow, $keterangan);
        $sheet->getStyle('A' . $keteranganRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Auto size columns
        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height untuk keterangan
        $sheet->getRowDimension($keteranganRow)->setRowHeight(120);

        // Generate file
        $writer = new Xlsx($spreadsheet);

        $fileName = 'Template_Alternatif_' . date('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/public/templates/' . $fileName);

        // Pastikan folder ada
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // Template untuk Kriteria
    public function downloadTemplateKriteria()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul sheet
        $sheet->setTitle('Template Kriteria');

        // Header
        $headers = ['nama', 'bobot', 'jenis'];

        // Set header di row 1
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Style header
        $headerRange = 'A1:C1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Data contoh
        $contohData = [
            ['Penghasilan dibawah UMK', 5, 'cost'],
            ['WNII', 4, 'benefit'],
            ['Sudah berkeluarga', 4, 'benefit'],
            ['Memiliki Rumah Tidak Layak', 3, 'benefit'],
            ['Menguasai Tanah', 2, 'benefit']
        ];

        // Input data contoh
        $row = 2;
        foreach ($contohData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Style data rows - highlight cost dengan warna berbeda
        for ($i = 2; $i <= count($contohData) + 1; $i++) {
            $jenisValue = $sheet->getCell('C' . $i)->getValue();
            if ($jenisValue == 'cost') {
                $sheet->getStyle('A' . $i . ':C' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6'] // Light red untuk cost
                    ]
                ]);
            } else {
                $sheet->getStyle('A' . $i . ':C' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6F7E6'] // Light green untuk benefit
                    ]
                ]);
            }
        }

        // Keterangan
        $keteranganRow = $row + 1;
        $sheet->mergeCells('A' . $keteranganRow . ':C' . ($keteranganRow + 8));

        $keterangan = "KETERANGAN:\n\n";
        $keterangan .= "FIELD:\n";
        $keterangan .= "- nama: Nama kriteria (contoh: Penghasilan dibawah UMK)\n";
        $keterangan .= "- bobot: Nilai bobot kriteria (angka)\n";
        $keterangan .= "- jenis: cost atau benefit\n\n";
        $keterangan .= "JENIS KRITERIA:\n";
        $keterangan .= "- cost: Semakin kecil nilai semakin baik (background merah)\n";
        $keterangan .= "- benefit: Semakin besar nilai semakin baik (background hijau)\n\n";
        $keterangan .= "CATATAN:\n";
        $keterangan .= "- Bobot akan dinormalisasi otomatis oleh sistem\n";
        $keterangan .= "- Jenis harus diisi dengan 'cost' atau 'benefit' (huruf kecil)";

        $sheet->setCellValue('A' . $keteranganRow, $keterangan);
        $sheet->getStyle('A' . $keteranganRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ],
            'font' => [
                'size' => 10
            ]
        ]);

        // Auto size columns
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);

        // Set row height untuk keterangan
        $sheet->getRowDimension($keteranganRow)->setRowHeight(200);

        // Generate file
        $writer = new Xlsx($spreadsheet);

        $fileName = 'Template_Kriteria_' . date('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/public/templates/' . $fileName);

        // Pastikan folder ada
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // Template gabungan (opsional)
    public function downloadTemplateKombinasi()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Kriteria
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Kriteria');
        
        // Header kriteria
        $sheet1->setCellValue('A1', 'nama');
        $sheet1->setCellValue('B1', 'bobot');
        $sheet1->setCellValue('C1', 'jenis');
        
        // Style header
        $sheet1->getStyle('A1:C1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);
        
        // Data contoh kriteria
        $kriteriaData = [
            ['Penghasilan dibawah UMK', 5, 'cost'],
            ['WNII', 4, 'benefit'],
            ['Sudah berkeluarga', 4, 'benefit'],
            ['Memiliki Rumah Tidak Layak', 3, 'benefit'],
            ['Menguasai Tanah', 2, 'benefit']
        ];
        
        $row = 2;
        foreach ($kriteriaData as $data) {
            $sheet1->setCellValue('A' . $row, $data[0]);
            $sheet1->setCellValue('B' . $row, $data[1]);
            $sheet1->setCellValue('C' . $row, $data[2]);
            $row++;
        }
        
        // Sheet 2: Alternatif
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Alternatif');
        
        // Header alternatif
        $headers = ['kode', 'nama', 'C1', 'C2', 'C3', 'C4', 'C5'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style header
        $sheet2->getStyle('A1:G1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);
        
        // Data contoh alternatif
        $alternatifData = [
            ['A1', 'Contoh Nama 1', 500, 70, 10, 80, 3000],
            ['A2', 'Contoh Nama 2', 300, 90, 10, 60, 2500],
            ['A3', 'Contoh Nama 3', 400, 80, 9, 90, 2000]
        ];
        
        $row = 2;
        foreach ($alternatifData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet2->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto size semua kolom
        foreach (['A', 'B', 'C'] as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Template_Lengkap_' . date('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/public/templates/' . $fileName);
        
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        $writer->save($filePath);
        
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
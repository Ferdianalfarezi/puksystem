<?php

namespace App\Exports;

use App\Models\HistoryKas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $kasId;
    protected $year;
    protected $month;
    protected $totalMasuk;
    protected $totalKeluar;
    protected $saldoAwal;
    protected $saldoAkhir;
    protected $rowCount = 0;

    public function __construct($kasId, $year = null, $month = null, $totalMasuk = 0, $totalKeluar = 0, $saldoAwal = 0, $saldoAkhir = 0)
    {
        $this->kasId = $kasId;
        $this->year = $year;
        $this->month = $month;
        $this->totalMasuk = $totalMasuk;
        $this->totalKeluar = $totalKeluar;
        $this->saldoAwal = $saldoAwal;
        $this->saldoAkhir = $saldoAkhir;
    }

    public function collection()
    {
        $query = HistoryKas::with(['dilakukanOleh'])
            ->where('kas_id', $this->kasId);

        if ($this->year) {
            $query->whereYear('tanggal_transaksi', $this->year);
        }

        if ($this->month) {
            $query->whereMonth('tanggal_transaksi', $this->month);
        }

        $histories = $query->latest('tanggal_transaksi')->get();
        $this->rowCount = $histories->count();
        
        return $histories;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Status',
            'Sumber',
            'Keterangan',
            'Jumlah',
            'Saldo Sebelum',
            'Saldo Sesudah',
            'Dilakukan Oleh',
        ];
    }

    public function map($history): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $history->tanggal_transaksi->format('d-m-Y'),
            $history->tanggal_transaksi->format('H:i'),
            $history->jenis === 'masuk' ? 'Uang Masuk' : 'Uang Keluar',
            ucfirst($history->sumber),
            $history->keterangan,
            ($history->jenis === 'masuk' ? '+' : '-') . ' Rp ' . number_format($history->jumlah, 0, ',', '.'),
            'Rp ' . number_format($history->saldo_sebelum, 0, ',', '.'),
            'Rp ' . number_format($history->saldo_sesudah, 0, ',', '.'),
            $history->dilakukanOleh->name ?? 'Unknown',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // All data borders
        $lastRow = $this->rowCount + 1;
        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Center align untuk kolom tertentu
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 12,  // Tanggal
            'C' => 8,   // Waktu
            'D' => 15,  // Status
            'E' => 12,  // Sumber
            'F' => 40,  // Keterangan
            'G' => 18,  // Jumlah
            'H' => 18,  // Saldo Sebelum
            'I' => 18,  // Saldo Sesudah
            'J' => 20,  // Dilakukan Oleh
        ];
    }

    public function title(): string
    {
        return 'Laporan Kas';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 1;
                
                // Add summary section
                $summaryRow = $lastRow + 2;
                
                // Title summary
                $sheet->setCellValue("A{$summaryRow}", 'RINGKASAN');
                $sheet->mergeCells("A{$summaryRow}:J{$summaryRow}");
                $sheet->getStyle("A{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Summary data
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", 'Saldo Awal Periode');
                $sheet->setCellValue("B{$summaryRow}", 'Rp ' . number_format($this->saldoAwal, 0, ',', '.'));
                
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", 'Total Kas Masuk');
                $sheet->setCellValue("B{$summaryRow}", 'Rp ' . number_format($this->totalMasuk, 0, ',', '.'));
                $sheet->getStyle("B{$summaryRow}")->getFont()->getColor()->setRGB('059669');
                
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", 'Total Kas Keluar');
                $sheet->setCellValue("B{$summaryRow}", 'Rp ' . number_format($this->totalKeluar, 0, ',', '.'));
                $sheet->getStyle("B{$summaryRow}")->getFont()->getColor()->setRGB('DC2626');
                
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", 'Saldo Akhir Periode');
                $sheet->setCellValue("B{$summaryRow}", 'Rp ' . number_format($this->saldoAkhir, 0, ',', '.'));
                $sheet->getStyle("A{$summaryRow}:B{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF3C7'],
                    ],
                ]);

                // Make summary bold
                $summaryStartRow = $lastRow + 3;
                $summaryEndRow = $lastRow + 7;
                $sheet->getStyle("A{$summaryStartRow}:A{$summaryEndRow}")->getFont()->setBold(true);
                $sheet->mergeCells("B{$summaryStartRow}:J{$summaryStartRow}");
                $sheet->mergeCells("B" . ($summaryStartRow + 1) . ":J" . ($summaryStartRow + 1));
                $sheet->mergeCells("B" . ($summaryStartRow + 2) . ":J" . ($summaryStartRow + 2));
                $sheet->mergeCells("B" . ($summaryStartRow + 3) . ":J" . ($summaryStartRow + 3));
            },
        ];
    }
}
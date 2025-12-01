<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return sample data
        return [
            [
                'John Doe',
                'johndoe',
                'password123',
                'Admin',
                'Bidang Organisasi',
                'active'
            ],
            [
                'Jane Smith',
                'janesmith',
                'password123',
                'Anggota',
                'Bidang Organisasi',
                'active'
            ],
            [
                'Bob Johnson',
                'bobjohnson',
                'password123',
                'Anggota',
                'Bidang Organisasi',
                'active'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'username',
            'password',
            'role',
            'bidang',
            'status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Style untuk data rows
            'A2:F4' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,  // name
            'B' => 20,  // username
            'C' => 20,  // password
            'D' => 20,  // role
            'E' => 20,  // bidang
            'F' => 15,  // status
        ];
    }
}
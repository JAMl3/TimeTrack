<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimesheetExport implements FromCollection, WithHeadings, WithStyles
{
    protected $timeLogs;

    public function __construct($timeLogs)
    {
        $this->timeLogs = $timeLogs;
    }

    public function collection()
    {
        return $this->timeLogs;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Employee Number',
            'Date',
            'Clock In',
            'Clock Out',
            'Hours Worked',
            'Is Late',
            'Left Early'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:H1' => ['fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F8F9FA']]],
        ];
    }
}

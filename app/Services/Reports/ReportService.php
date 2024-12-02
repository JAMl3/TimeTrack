<?php

namespace App\Services\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Reports\Generators\PdfGenerator;
use App\Services\Reports\Generators\CsvGenerator;

abstract class ReportService
{
    protected $startDate;
    protected $endDate;
    protected $departmentId;
    protected $employeeId;

    public function __construct(Request $request)
    {
        $this->startDate = Carbon::parse($request->start_date)->startOfDay();
        $this->endDate = Carbon::parse($request->end_date)->endOfDay();
        $this->departmentId = $request->department_id;
        $this->employeeId = $request->employee_id;
    }

    abstract protected function getData(): array;
    abstract protected function getViewName(): string;
    abstract protected function getFileName(): string;

    public function generate(string $format = 'pdf')
    {
        $data = $this->getData();

        return $format === 'pdf'
            ? (new PdfGenerator())->generate($this->getViewName(), $data, $this->getFileName())
            : (new CsvGenerator())->generate($data, $this->getHeaders(), $this->getFileName());
    }

    protected function getDateRange(): string
    {
        return $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y');
    }

    protected function getHeaders(): array
    {
        return [];
    }
}

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

        if ($format === 'pdf') {
            return (new PdfGenerator())->generate($this->getViewName(), $data, $this->getFileName());
        }

        // For CSV, transform the data into rows based on report type
        $rows = [];

        // Basic Clocking Report and Timesheet Report
        if (isset($data['timeLogs'])) {
            foreach ($data['timeLogs'] as $log) {
                $rows[] = [
                    $log['employee_name'] ?? ($data['employee']['name'] ?? ''),
                    $log['department'] ?? ($data['employee']['department'] ?? ''),
                    $log['date'],
                    $log['clock_in'],
                    $log['clock_out'],
                    $log['duration'],
                    $log['status'],
                    $log['is_late'] ? 'Yes' : 'No',
                    $log['left_early'] ? 'Yes' : 'No',
                    $log['notes'] ?? ''
                ];
            }
        }
        // Department Overview Report
        elseif (isset($data['departments'])) {
            foreach ($data['departments'] as $dept) {
                $rows[] = [
                    $dept['department'],
                    $dept['total_employees'],
                    $dept['attendance_rate'] . '%',
                    $dept['late_rate'] . '%',
                    $dept['early_departure_rate'] . '%',
                    $dept['average_hours']
                ];
            }
        }
        // Holiday Summary Report
        elseif (isset($data['records'])) {
            foreach ($data['records'] as $record) {
                $monthlyPattern = implode(', ', array_map(function ($month, $count) {
                    return "$month: $count";
                }, array_keys($record['leave_pattern']), $record['leave_pattern']));

                $rows[] = [
                    $record['employee_name'],
                    $record['department'],
                    $record['total_entitlement'],
                    $record['days_taken'],
                    $record['days_remaining'],
                    $record['pending_requests'],
                    $monthlyPattern
                ];
            }
        }
        // Absence Patterns Report
        elseif (isset($data['patterns'])) {
            foreach ($data['patterns'] as $pattern) {
                $rows[] = [
                    $pattern['employee_name'],
                    $pattern['department'],
                    $pattern['total_absences'],
                    $pattern['most_common_day'],
                    $pattern['day_frequency'],
                    $pattern['max_consecutive'],
                    $pattern['consecutive_occurrences'],
                    $pattern['weekly_frequency']
                ];
            }
        }
        // Extended Absences Report
        elseif (isset($data['absences'])) {
            foreach ($data['absences'] as $absence) {
                foreach ($absence['absence_periods'] as $period) {
                    $rows[] = [
                        $absence['employee_name'],
                        $absence['employee_number'],
                        $absence['department'],
                        $period['start_date'],
                        $period['end_date'],
                        $period['duration'],
                        $period['reason']
                    ];
                }
            }
        }

        return (new CsvGenerator())->generate($rows, $this->getHeaders(), $this->getFileName());
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

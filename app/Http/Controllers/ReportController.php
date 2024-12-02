<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\GenerateReportRequest;
use Illuminate\Support\Facades\Log;
use App\Services\Reports\BasicClockingReport;
use App\Services\Reports\DepartmentOverviewReport;
use App\Services\Reports\TimesheetReport;
use App\Services\Reports\HolidaySummaryReport;
use App\Services\Reports\AbsencePatternsReport;
use App\Services\Reports\ExtendedAbsencesReport;

class ReportController extends Controller
{
    private const REPORT_SERVICES = [
        'basic-clocking' => BasicClockingReport::class,
        'department-overview' => DepartmentOverviewReport::class,
        'timesheet' => TimesheetReport::class,
        'holiday-summary' => HolidaySummaryReport::class,
        'absence-patterns' => AbsencePatternsReport::class,
        'extended-absences' => ExtendedAbsencesReport::class,
    ];

    public function index()
    {
        $this->authorize('view reports');
        $departments = Department::all();

        $navigation = [
            'reports' => [
                'children' => [
                    'basic-clocking' => ['name' => 'Basic Clocking Report'],
                    'department-overview' => ['name' => 'Department Overview'],
                    'timesheet' => ['name' => 'Employee Timesheet'],
                    'holiday-summary' => ['name' => 'Holiday Summary'],
                    'absence-patterns' => ['name' => 'Absence Patterns'],
                    'extended-absences' => ['name' => 'Extended Absences'],
                ]
            ]
        ];

        return view('reports.index', compact('departments', 'navigation'));
    }

    public function generate(GenerateReportRequest $request)
    {
        try {
            $serviceClass = self::REPORT_SERVICES[$request->report_type] ?? null;

            if (!$serviceClass) {
                Log::warning('Invalid report type requested', [
                    'report_type' => $request->report_type,
                    'available_types' => array_keys(self::REPORT_SERVICES)
                ]);
                return back()->with('error', 'Invalid report type selected.');
            }

            $service = new $serviceClass($request);
            return $service->generate($request->format);
        } catch (\Exception $e) {
            Log::error('Report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->with('error', 'Error generating report: ' . $e->getMessage());
        }
    }
}

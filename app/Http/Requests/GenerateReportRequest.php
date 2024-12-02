<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('view-reports');
    }

    public function rules(): array
    {
        return [
            'report_type' => 'required|string|in:basic-clocking,department-overview,timesheet,holiday-summary,absence-patterns,extended-absences',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'required_if:report_type,timesheet|exists:employees,id',
            'format' => 'required|in:pdf,csv'
        ];
    }

    public function messages(): array
    {
        return [
            'report_type.in' => 'The selected report type is invalid.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'employee_id.required_if' => 'An employee must be selected for timesheet reports.',
        ];
    }
}

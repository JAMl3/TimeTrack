<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Absence Patterns Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .warning {
            color: #f59e0b;
        }

        .danger {
            color: #dc2626;
        }

        .good {
            color: #16a34a;
        }

        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .summary h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .summary-item {
            margin-bottom: 8px;
        }

        .summary-label {
            font-weight: bold;
            color: #4b5563;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Absence Patterns Report</h1>
        <p>{{ $data['dateRange'] }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Employees with Absences:</span>
                <span>{{ $data['summary']['total_employees_with_absences'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Absence Days:</span>
                <span>{{ $data['summary']['total_absence_days'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Average Absences:</span>
                <span>{{ $data['summary']['average_absences'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">High Frequency Employees:</span>
                <span>{{ $data['summary']['high_frequency_employees'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Most Common Day:</span>
                <span>{{ $data['summary']['most_common_day'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Highest Absence Rate:</span>
                <span>{{ $data['summary']['highest_absence_rate'] }}%</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Monday/Friday Pattern:</span>
                <span>{{ $data['summary']['monday_friday_pattern'] }} employees</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Working Days in Period:</span>
                <span>{{ $data['summary']['working_days_in_period'] }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Total Absences</th>
                <th>Total Days</th>
                <th>Absence Rate</th>
                <th>Most Common Day</th>
                <th>Day Frequency</th>
                <th>Max Consecutive</th>
                <th>Weekly Freq.</th>
                <th>Type</th>
                <th>Mon/Fri %</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['patterns'] as $pattern)
                <tr>
                    <td>{{ $pattern['employee_name'] }}</td>
                    <td>{{ $pattern['department'] }}</td>
                    <td
                        class="{{ $pattern['total_absences'] >= 10 ? 'danger' : ($pattern['total_absences'] >= 5 ? 'warning' : 'good') }}">
                        {{ $pattern['total_absences'] }}
                    </td>
                    <td>{{ $pattern['total_days'] }}</td>
                    <td
                        class="{{ $pattern['absence_rate'] >= 20 ? 'danger' : ($pattern['absence_rate'] >= 10 ? 'warning' : 'good') }}">
                        {{ number_format($pattern['absence_rate'], 1) }}%
                    </td>
                    <td>{{ $pattern['most_common_day'] }}</td>
                    <td
                        class="{{ $pattern['day_frequency'] >= 4 ? 'danger' : ($pattern['day_frequency'] >= 2 ? 'warning' : 'good') }}">
                        {{ $pattern['day_frequency'] }}
                    </td>
                    <td
                        class="{{ $pattern['max_consecutive'] >= 5 ? 'danger' : ($pattern['max_consecutive'] >= 3 ? 'warning' : 'good') }}">
                        {{ $pattern['max_consecutive'] }}
                    </td>
                    <td
                        class="{{ $pattern['weekly_frequency'] >= 1 ? 'danger' : ($pattern['weekly_frequency'] >= 0.5 ? 'warning' : 'good') }}">
                        {{ number_format($pattern['weekly_frequency'], 1) }}
                    </td>
                    <td>{{ $pattern['most_common_type'] }}</td>
                    <td
                        class="{{ $pattern['monday_rate'] + $pattern['friday_rate'] >= 40 ? 'danger' : ($pattern['monday_rate'] + $pattern['friday_rate'] >= 25 ? 'warning' : 'good') }}">
                        M: {{ number_format($pattern['monday_rate'], 1) }}%<br>
                        F: {{ number_format($pattern['friday_rate'], 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($data['patterns']))
        <div style="page-break-before: always;">
            <h2>Detailed Analysis</h2>
            @foreach ($data['patterns'] as $pattern)
                <div style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd;">
                    <h3>{{ $pattern['employee_name'] }} ({{ $pattern['department'] }})</h3>
                    <p><strong>Period:</strong> {{ $pattern['first_absence_date'] }} to
                        {{ $pattern['last_absence_date'] }}</p>

                    <h4>Absence Types</h4>
                    <ul>
                        @foreach ($pattern['type_breakdown'] as $type => $count)
                            <li>{{ ucfirst($type) }}: {{ $count }} occurrences</li>
                        @endforeach
                    </ul>

                    <h4>Pattern Indicators</h4>
                    <ul>
                        <li>Most common reason: {{ $pattern['most_common_reason'] }}
                            ({{ $pattern['reason_frequency'] }} times)</li>
                        <li>Consecutive absences: {{ $pattern['consecutive_occurrences'] }} occurrences of
                            {{ $pattern['max_consecutive'] }} days</li>
                        <li>Weekly frequency: {{ number_format($pattern['weekly_frequency'], 1) }} absences per week
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</body>

</html>

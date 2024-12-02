<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Attendance Summary Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .status-count {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 5px;
        }

        .late {
            background-color: #ffebee;
            color: #dc3545;
        }

        .early {
            background-color: #fff8e1;
            color: #ffc107;
        }

        .on-time {
            background-color: #e8f5e9;
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Attendance Summary Report</h1>
        <p>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    @foreach($employeeStats as $employeeName => $stats)
        <div class="summary-box">
            <h3>{{ $employeeName }}</h3>

            <div style="margin-bottom: 15px;">
                <div class="status-count late">
                    Late Arrivals: {{ $stats['late_count'] }}
                </div>
                <div class="status-count early">
                    Early Departures: {{ $stats['early_count'] }}
                </div>
                <div class="status-count on-time">
                    On Time: {{ $stats['on_time_count'] }}
                </div>
            </div>

            <table>
                <tr>
                    <th>Total Days Worked</th>
                    <td>{{ $stats['total_days'] }}</td>
                </tr>
                <tr>
                    <th>Total Hours</th>
                    <td>{{ $stats['total_hours'] }}</td>
                </tr>
                <tr>
                    <th>Average Hours per Day</th>
                    <td>{{ $stats['total_days'] > 0 ? round($stats['total_hours'] / $stats['total_days'], 2) : 0 }}</td>
                </tr>
                <tr>
                    <th>Punctuality Rate</th>
                    <td>{{ $stats['total_days'] > 0 ? round(($stats['on_time_count'] / $stats['total_days']) * 100, 1) }}%</td>
                </tr>
            </table>
        </div>
    @endforeach

    <div style="margin-top: 20px;">
        <h3>Department Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Late Arrivals</td>
                    <td>{{ $departmentStats['total_late'] }}</td>
                    <td>{{ $departmentStats['total_days'] > 0 ? round(($departmentStats['total_late'] / $departmentStats['total_days']) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Total Early Departures</td>
                    <td>{{ $departmentStats['total_early'] }}</td>
                    <td>{{ $departmentStats['total_days'] > 0 ? round(($departmentStats['total_early'] / $departmentStats['total_days']) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Total On Time</td>
                    <td>{{ $departmentStats['total_on_time'] }}</td>
                    <td>{{ $departmentStats['total_days'] > 0 ? round(($departmentStats['total_on_time'] / $departmentStats['total_days']) * 100, 1) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px; text-align: right;">
        <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>

</html>

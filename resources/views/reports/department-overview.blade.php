<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Department Overview Report</title>
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
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .metric {
            font-weight: bold;
        }

        .good {
            color: #16a34a;
        }

        .warning {
            color: #f59e0b;
        }

        .poor {
            color: #dc2626;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Department Overview Report</h1>
        <p>{{ $dateRange }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Total Employees</th>
                <th>Attendance Rate</th>
                <th>Late Rate</th>
                <th>Early Departure Rate</th>
                <th>Average Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $department)
                <tr>
                    <td>{{ $department['department'] }}</td>
                    <td>{{ $department['total_employees'] }}</td>
                    <td
                        class="metric {{ $department['attendance_rate'] >= 90 ? 'good' : ($department['attendance_rate'] >= 80 ? 'warning' : 'poor') }}">
                        {{ $department['attendance_rate'] }}%
                    </td>
                    <td
                        class="metric {{ $department['late_rate'] <= 5 ? 'good' : ($department['late_rate'] <= 10 ? 'warning' : 'poor') }}">
                        {{ $department['late_rate'] }}%
                    </td>
                    <td
                        class="metric {{ $department['early_departure_rate'] <= 5 ? 'good' : ($department['early_departure_rate'] <= 10 ? 'warning' : 'poor') }}">
                        {{ $department['early_departure_rate'] }}%
                    </td>
                    <td
                        class="metric {{ $department['average_hours'] >= 7.5 ? 'good' : ($department['average_hours'] >= 7 ? 'warning' : 'poor') }}">
                        {{ $department['average_hours'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

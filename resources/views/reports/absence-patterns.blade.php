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

        .warning {
            color: #f59e0b;
        }

        .danger {
            color: #dc2626;
        }

        .good {
            color: #16a34a;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Absence Patterns Report</h1>
        <p>{{ $data['dateRange'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Total Absences</th>
                <th>Most Common Day</th>
                <th>Day Frequency</th>
                <th>Max Consecutive Days</th>
                <th>Weekly Frequency</th>
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
                        class="{{ $pattern['frequency'] >= 1 ? 'danger' : ($pattern['frequency'] >= 0.5 ? 'warning' : 'good') }}">
                        {{ $pattern['frequency'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Late Arrival Patterns Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .date-range {
            text-align: center;
            color: #666;
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
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
        }

        .trend-increasing {
            color: #ef4444;
        }

        .trend-decreasing {
            color: #22c55e;
        }

        .trend-stable {
            color: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Late Arrival Patterns Report</h1>
    </div>
    <div class="date-range">
        {{ $data['dateRange'] }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Total Late Arrivals</th>
                <th>Avg. Minutes Late</th>
                <th>Most Common Day</th>
                <th>Trend</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patterns as $employeeName => $data)
                <tr>
                    <td>{{ $employeeName }}</td>
                    <td>{{ $data['department'] }}</td>
                    <td>{{ $data['total_lates'] }}</td>
                    <td>{{ round($data['average_minutes_late'], 1) }}</td>
                    <td>{{ $data['most_common_day'] }}</td>
                    <td class="trend-{{ strtolower($data['trend']) }}">{{ $data['trend'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>

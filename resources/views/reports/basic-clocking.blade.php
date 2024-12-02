<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Basic Clocking Report</title>
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

        .late {
            color: #dc2626;
        }

        .early {
            color: #f59e0b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Basic Clocking Report</h1>
        <p>{{ $data['dateRange'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['timeLogs'] as $log)
                <tr>
                    <td>{{ $log['employee_name'] }}</td>
                    <td>{{ $log['department'] }}</td>
                    <td>{{ $log['date'] }}</td>
                    <td @if ($log['is_late']) class="late" @endif>
                        {{ $log['clock_in'] }}
                    </td>
                    <td @if ($log['left_early']) class="early" @endif>
                        {{ $log['clock_out'] }}
                    </td>
                    <td>{{ $log['duration'] }}</td>
                    <td>{{ $log['status'] }}</td>
                    <td>{{ $log['notes'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Timesheet Report</title>
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

        .employee-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .employee-info p {
            margin: 5px 0;
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

        .present {
            color: #16a34a;
        }

        .absent {
            color: #dc2626;
        }

        .leave {
            color: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Employee Timesheet Report</h1>
        <p>{{ $data['dateRange'] }}</p>
    </div>

    <div class="employee-info">
        <p><strong>Employee:</strong> {{ $data['employee']['name'] }}</p>
        <p><strong>Employee Number:</strong> {{ $data['employee']['number'] }}</p>
        <p><strong>Department:</strong> {{ $data['employee']['department'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
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
                    <td>{{ $log['date'] }}</td>
                    <td @if ($log['is_late']) class="late" @endif>
                        {{ $log['clock_in'] }}
                    </td>
                    <td @if ($log['left_early']) class="early" @endif>
                        {{ $log['clock_out'] }}
                    </td>
                    <td>{{ $log['duration'] }}</td>
                    <td class="{{ strtolower($log['status']) }}">
                        {{ $log['status'] }}
                    </td>
                    <td>{{ $log['notes'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Holiday Summary Report</title>
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
        <h1>Holiday Summary Report</h1>
        <p>{{ $data['dateRange'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Total Entitlement</th>
                <th>Days Taken</th>
                <th>Days Remaining</th>
                <th>Pending Requests</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['records'] as $record)
                <tr>
                    <td>{{ $record['employee_name'] }}</td>
                    <td>{{ $record['department'] }}</td>
                    <td>{{ $record['total_entitlement'] }}</td>
                    <td>{{ $record['days_taken'] }}</td>
                    <td
                        class="{{ $record['days_remaining'] <= 5 ? 'warning' : ($record['days_remaining'] <= 2 ? 'danger' : 'good') }}">
                        {{ $record['days_remaining'] }}
                    </td>
                    <td>{{ $record['pending_requests'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

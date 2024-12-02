<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leave Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .date-range {
            color: #666;
            margin-bottom: 20px;
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
            background-color: #f8f9fa;
        }

        .page-break {
            page-break-after: always;
        }

        .leave-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.9em;
        }

        .annual {
            background: #e3f2fd;
        }

        .sick {
            background: #ffebee;
        }

        .other {
            background: #f5f5f5;
        }

        .error {
            color: #dc3545;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #dc3545;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Leave Report</h1>
        <div class="date-range">
            {{ $data['dateRange'] }}
        </div>
    </div>

    @if (empty($data['records']))
        <div class="error">
            No leave records found for the selected date range.
        </div>
    @else
        @foreach ($data['records'] as $employeeName => $employeeData)
            <h3>{{ $employeeName }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employeeData['leaves'] as $leave)
                        <tr>
                            <td>
                                <span class="leave-type {{ strtolower($leave['type']) }}">
                                    {{ $leave['type'] }}
                                </span>
                            </td>
                            <td>{{ $leave['start_date'] }}</td>
                            <td>{{ $leave['end_date'] }}</td>
                            <td>{{ $leave['days'] }}</td>
                            <td>{{ $leave['status'] }}</td>
                            <td>{{ $leave['reason'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (isset($employeeData['balance']))
                <div style="margin-bottom: 20px;">
                    <strong>Leave Balance:</strong>
                    <ul>
                        <li>Annual Leave: {{ $employeeData['balance']['annual'] }} days</li>
                        <li>Sick Leave: {{ $employeeData['balance']['sick'] }} days</li>
                    </ul>
                </div>
            @endif

            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div style="margin-top: 20px; text-align: right;">
        <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>

</html>

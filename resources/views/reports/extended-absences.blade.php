<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Extended Absences Report</title>
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

        .employee-section {
            margin-bottom: 30px;
        }

        .extension-note {
            color: #666;
            font-style: italic;
        }

        .days-count {
            font-weight: bold;
            color: #ef4444;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Extended Absences Report</h1>
    </div>
    <div class="date-range">
        {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
    </div>

    @foreach ($extendedAbsences as $employeeName => $data)
        <div class="employee-section">
            <h2>{{ $employeeName }} - {{ $data['department'] }}</h2>
            <p>Total Extended Absence Days: <span class="days-count">{{ $data['total_extended_days'] }}</span></p>
            @if ($data['extension_preference'] > 0)
                <p class="extension-note">Note: Future absences will be automatically extended by
                    {{ $data['extension_preference'] }} day(s)</p>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration (Days)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['periods'] as $period)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($period['start_date'])->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($period['end_date'])->format('d M Y') }}</td>
                            <td>{{ $period['days'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>

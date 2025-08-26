<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        h2, h4 { margin: 0; }
    </style>
</head>
<body>
    <h2>Attendance Report</h2>
    @if($date)
        <h4>Date: {{ $date }}</h4>
    @elseif($start_date && $end_date)
        <h4>From {{ $start_date }} to {{ $end_date }}</h4>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Sign-in Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->sign_in_time }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

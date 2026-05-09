<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card - {{ $student->fullname }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; pb: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .grade-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .grade-table th, .grade-table td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .grade-table th { bg-color: #f4f4f4; }
        .footer { margin-top: 50px; text-align: right; font-size: 0.9em; }
        .signature { margin-top: 40px; border-top: 1px solid #000; width: 200px; float: right; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>SCHOOL NAME HERE</h2>
        <p>Official Student Report Card</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Name:</strong> {{ $student->fullname }}</td>
            <td><strong>LRN:</strong> {{ $student->lrn }}</td>
        </tr>
        <tr>
            <td><strong>Level:</strong> {{ $student->level }} High School</td>
            <td><strong>Date Generated:</strong> {{ date('M d, Y') }}</td>
        </tr>
    </table>

    <table class="grade-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Quarter 1</th>
                <th>Quarter 2</th>
                <th>Quarter 3</th>
                <th>Quarter 4</th>
                <th>Final Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $grade)
            <tr>
                <td style="text-align: left;">{{ $grade->subject }}</td>
                <td>{{ $grade->q1 ?? '-' }}</td>
                <td>{{ $grade->q2 ?? '-' }}</td>
                <td>{{ $grade->q3 ?? '-' }}</td>
                <td>{{ $grade->q4 ?? '-' }}</td>
                <td><strong>{{ $grade->final_grade ?? '-' }}</strong></td>
                <td>
                    <span style="color: {{ $grade->final_grade >= 75 ? 'green' : 'red' }}">
                        {{ $grade->final_grade >= 75 ? 'PASSED' : 'FAILED' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No published grades found for this student.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>School Administrator</p>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print to PDF
        </button>
    </div>

</body>
</html>
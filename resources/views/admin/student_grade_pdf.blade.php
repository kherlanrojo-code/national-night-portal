<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Report - {{ $student->fullname }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; padding: 40px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 2px; }
        .header h3 { margin: 5px 0; font-weight: normal; color: #666; }
        
        .student-info { margin-bottom: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .student-info p { margin: 5px 0; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 12px; text-align: left; }
        th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        
        .grade-pass { color: green; font-weight: bold; }
        .grade-fail { color: red; font-weight: bold; }
        
        /* Signature Section Styles */
        .signatory-container { 
            margin-top: 60px; 
            display: flex; 
            justify-content: space-between; 
            text-align: center; 
        }
        .sig-box { width: 40%; }
        .sig-line { border-top: 1px solid #000; margin-bottom: 5px; font-weight: bold; text-transform: uppercase; font-size: 14px; }
        .sig-label { font-size: 12px; color: #555; font-style: italic; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
            table { font-size: 12px; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: center; background: #f1f5f9; padding: 15px; border-radius: 10px;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
            <i class="fas fa-print"></i> Confirm & Print to PDF
        </button>
        <a href="{{ url()->previous() }}" style="margin-left: 15px; text-decoration: none; color: #64748b; font-size: 14px;">Cancel & Go Back</a>
    </div>

    <div class="header">
        <h1>NATIONAL NIGHT PORTAL</h1>
        <h3>Official Student Progress Report</h3>
    </div>

    <div class="student-info">
        <div>
            <p><strong>NAME:</strong> {{ strtoupper($student->fullname) }}</p>
            <p><strong>LRN:</strong> {{ $student->lrn }}</p>
        </div>
        <div style="text-align: right;">
            <p><strong>LEVEL:</strong> {{ $student->level }} High School</p>
            <p><strong>DATE ISSUED:</strong> {{ date('F d, Y') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Subject Description</th>
                <th>Semester</th>
                <th>Final Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $grade)
            <tr>
                <td>{{ $grade->subject }}</td>
                <td>{{ $grade->semester }}</td>
                <td>{{ number_format($grade->grade, 2) }}</td>
                <td class="{{ $grade->grade < 75 ? 'grade-fail' : 'grade-pass' }}">
                    {{ $grade->grade < 75 ? 'FAILED' : 'PASSED' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #999;">No grades recorded for this student.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatory-container">
        <div class="sig-box">
            <div class="sig-line">{{ $signatories->registrar ?? '__________________________' }}</div>
            <div class="sig-label">School Registrar / Admin</div>
        </div>

        <div class="sig-box">
            <div class="sig-line">{{ $signatories->school_head ?? '__________________________' }}</div>
            <div class="sig-label">School Head / Principal</div>
        </div>
    </div>

</body>
</html>
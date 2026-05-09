<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Report - {{ $student->fullname }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .print-container { border: none; box-shadow: none; width: 100%; max-width: 100%; margin: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-4xl mx-auto bg-white p-8 border shadow-sm print-container">
        <div class="text-center border-b-2 border-slate-800 pb-4 mb-6">
            <h1 class="text-2xl font-bold uppercase">COMPOSTELA NHS EVENING CLASS</h1>
            <p class="text-sm text-gray-600">Official Student Progress Report</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8 bg-slate-50 p-4 rounded">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Student Name</p>
                <p class="font-bold text-lg capitalize">{{ $student->fullname }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">LRN</p>
                <p class="font-mono text-lg">{{ $student->lrn }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Level</p>
                <p>{{ $student->level }} High School</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Date Generated</p>
                <p>{{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="border border-slate-700 px-4 py-3 text-left">Subject Name</th>
                    <th class="border border-slate-700 px-4 py-3 text-center">Subject Code</th>
                    <th class="border border-slate-700 px-4 py-3 text-center">Term</th>
                    <th class="border border-slate-700 px-4 py-3 text-center">Final Grade</th>
                    <th class="border border-slate-700 px-4 py-3 text-center">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $grade)
                <tr>
                    <td class="border p-3 capitalize">{{ $grade->subject }}</td>
                    <td class="border p-3 text-center font-bold">{{ $grade->subject_code ?? 'N/A' }}</td>
                    <td class="border p-3 text-center">{{ $grade->semester }}</td>
                    <td class="border p-3 text-center font-bold">{{ number_format($grade->grade, 2) }}</td>
                    <td class="border p-3 text-center font-bold {{ $grade->grade < 75 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $grade->grade < 75 ? 'FAILED' : 'PASSED' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-black">
                    <td colspan="3" class="border p-4 text-right uppercase tracking-wider">General Average:</td>
                    <td class="border p-4 text-center text-xl text-blue-700">{{ number_format($gpa, 2) }}</td>
                    <td class="border p-4 text-center">
                        <span class="{{ $gpa >= 75 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $gpa >= 75 ? 'PASSED' : 'FAILED' }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-20 flex justify-between px-4">
            <div class="text-center w-64">
                <div class="font-bold uppercase text-sm min-h-[1.5rem]">
                    {{ $signatories->registrar ?? '' }}
                </div>
                <div class="border-b border-black mb-1"></div>
                <p class="text-xs font-bold uppercase">Registrar</p>
            </div>
            <div class="text-center w-64">
                <div class="font-bold uppercase text-sm min-h-[1.5rem]">
                    {{ $signatories->school_head ?? '' }}
                </div>
                <div class="border-b border-black mb-1"></div>
                <p class="text-xs font-bold uppercase">School Principal</p>
            </div>
        </div>

        <div class="mt-12 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 transition">
                <i class="fas fa-print mr-2"></i> Print Report Card Now
            </button>
        </div>
    </div>
</body>
</html>
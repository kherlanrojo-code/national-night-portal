<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Faculty Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-800 min-h-screen text-white p-4">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-slate-700 pb-4">Admin Panel</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Dashboard</a>
            <a href="{{ route('admin.monitoring') }}" class="block py-2.5 px-4 rounded bg-blue-600">Submission Tracker</a>
            <a href="{{ route('admin.studentList', 'Junior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Junior High</a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Senior High</a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Faculty Submission Monitoring</h1>
                <p class="text-sm text-slate-500">Tracking grade completion for <b>{{ $term }}</b></p>
            </div>

            <form action="{{ route('admin.monitoring') }}" method="GET">
                <select name="term" onchange="this.form.submit()" class="...">
                    <option value="1st term" {{ $term == '1st term' ? 'selected' : '' }}>1st Term</option>
                    <option value="2nd term" {{ $term == '2nd term' ? 'selected' : '' }}>2nd Term</option>
                    <option value="3rd term" {{ $term == '3rd term' ? 'selected' : '' }}>3rd Term</option>
                </select>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600">Teacher Name</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-center">Submission Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($teachers as $teacher)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800">
                            {{ $teacher->fullname }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{-- Logic: Marked COMPLETED only if actual_sent reaches (Students * 8) --}}
                            @if($teacher->expected_total > 0 && $teacher->actual_sent >= $teacher->expected_total)
                                <span class="text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full text-[10px] font-black border border-emerald-100 shadow-sm">
                                    <i class="fas fa-check-double mr-1"></i> COMPLETED
                                </span>
                            @elseif($teacher->actual_sent > 0 || $teacher->has_drafts)
                                {{-- Logic: Shows exactly how many of the 8 subjects per student are still missing --}}
                                <span class="text-orange-600 bg-orange-50 px-3 py-1 rounded-full text-[10px] font-black border border-orange-100">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> 
                                    PARTIAL ({{ $teacher->expected_total - $teacher->actual_sent }} MISSING)
                                </span>
                            @else
                                <span class="text-red-600 bg-red-50 px-3 py-1 rounded-full text-[10px] font-black border border-red-100">
                                    <i class="fas fa-times-circle mr-1"></i> NO SUBMISSION
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

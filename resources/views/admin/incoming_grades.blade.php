<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Incoming Grades</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-800 min-h-screen text-white p-4">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-slate-700 pb-4">Admin Panel</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.teachers') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Teacher's List</a>
            <a href="{{ route('admin.studentList', 'Junior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Junior High</a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Senior High</a>
            <a href="#" class="block py-2.5 px-4 rounded bg-blue-600">Incoming Grades</a>
            <a href="{{ route('admin.archive') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Archive</a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Incoming Grades from Advisers</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @forelse($incomingGrades as $lrn => $grades)
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Student LRN: <span class="text-blue-600">{{ $lrn }}</span></h3>
                        <p class="text-sm text-slate-500 italic">Grade submission review</p>
                    </div>
                    <form action="{{ route('admin.forwardGrades', $lrn) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700 shadow transition flex items-center">
                            <i class="fas fa-share-square mr-2"></i> Forward to Student
                        </button>
                    </form>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Subject Code & Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Grade</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($grades as $grade)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">
                                @php
                                    // The Fix: Find the subject code that matches BOTH the name and the student's level
                                    $actualSubject = \App\Models\Subject::where('name', $grade->subject)
                                                        ->where('level', $grade->level) 
                                                        ->first();
                                @endphp
                                <span class="font-bold text-slate-900">
                                    {{ $actualSubject->code ?? $grade->subject_code ?? 'N/A' }}
                                </span> 
                                - {{ $grade->subject }}
                            </td>
                            <!-- ... rest of your code ... -->
                            <td class="px-6 py-4 text-sm font-bold {{ $grade->grade < 75 ? 'text-red-600' : 'text-slate-800' }}">
                                {{ number_format($grade->grade, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending Approval</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="bg-white p-12 rounded-lg shadow text-center">
                <i class="fas fa-inbox text-slate-300 text-5xl mb-4"></i>
                <p class="text-slate-500 text-lg italic">No incoming grades to review right now.</p>
            </div>
        @endforelse
    </div>

</body>
</html>

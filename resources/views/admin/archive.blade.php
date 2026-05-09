<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Archive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-800 min-h-screen text-white p-4">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-slate-700 pb-4">Admin Panel</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Dashboard</a>
            <a href="{{ route('admin.teachers') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Teacher's List</a>
            <a href="{{ route('admin.studentList', 'Junior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Junior High</a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Senior High</a>
            <a href="{{ route('admin.incomingGrades') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Incoming Grades</a>
            <a href="#" class="block py-2.5 px-4 rounded bg-blue-600">Archive</a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Data Archive</h1>
            <p class="text-sm text-slate-500 italic">Manage deleted records and restorations</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="mb-10">
            <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center">
                <i class="fas fa-chalkboard-teacher mr-2 text-blue-600"></i> Archived Teachers
            </h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">ID / Name</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">Deleted At</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($archivedTeachers as $teacher)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700">{{ $teacher->fullname }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $teacher->employee_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $teacher->deleted_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.restoreTeacher', $teacher->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-100 transition">RESTORE</button>
                                </form>
                                <button onclick="confirmPermanentDelete('{{ $teacher->id }}', '{{ $teacher->fullname }}', 'teacher')" 
                                        class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                    PURGE
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">No archived teachers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center">
                <i class="fas fa-user-graduate mr-2 text-green-600"></i> Archived Students
            </h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">LRN / Name</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600">Level</th>
                            <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($archivedStudents as $student)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-700">{{ $student->fullname }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $student->lrn }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs uppercase font-bold">{{ $student->level }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.restoreStudent', $student->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-100 transition">RESTORE</button>
                                </form>
                                <button onclick="confirmPermanentDelete('{{ $student->id }}', '{{ $student->fullname }}', 'student')" 
                                        class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                                    PURGE
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">No archived students.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="purgeModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm shadow-2xl text-center">
            <div class="w-16 h-16 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Permanent Delete?</h3>
            <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                Warning: This action will permanently remove <span id="purgeName" class="font-bold text-red-600"></span> from the database. 
                This cannot be undone.
            </p>
            <form id="purgeForm" method="POST" class="mt-6 flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closePurgeModal()" class="flex-1 py-2 bg-slate-100 rounded-lg font-semibold text-slate-600 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">Delete Forever</button>
            </form>
        </div>
    </div>

    <script>
        function confirmPermanentDelete(id, name, type) {
            const modal = document.getElementById('purgeModal');
            const form = document.getElementById('purgeForm');
            const nameSpan = document.getElementById('purgeName');

            nameSpan.innerText = name;
            
            // Set dynamic route based on type
            if(type === 'teacher') {
                form.action = "/admin/teachers/force-delete/" + id;
            } else if(type === 'student') {
                // FIXED: Handle student purge route
                form.action = "/admin/students/force-delete/" + id;
            }

            modal.classList.remove('hidden');
        }

        function closePurgeModal() {
            document.getElementById('purgeModal').classList.add('hidden');
        }
    </script>
</body>
</html>
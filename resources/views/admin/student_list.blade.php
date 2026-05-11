<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ $level }} Masterlist</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-800 min-h-screen text-white p-4">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-slate-700 pb-4">Admin Panel</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Dashboard</a>
            <a href="{{ route('admin.teachers') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Teacher's List</a>
            <a href="{{ route('admin.studentList', 'Junior') }}" class="block py-2.5 px-4 rounded {{ $level == 'Junior' ? ' bg-blue-600' : 'hover:bg-slate-700' }}">Junior High</a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="block py-2.5 px-4 rounded {{ $level == 'Senior' ? ' bg-blue-600' : 'hover:bg-slate-700' }}">Senior High</a>
            <a href="{{ route('admin.incomingGrades') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Incoming Grades</a>
            <a href="{{ route('admin.archive') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Archive</a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $level }} High School Masterlist</h1>
                <p class="text-sm text-slate-500">Managing all students enrolled in {{ $level }} High.</p>
            </div>

            <div class="flex items-center space-x-4">
                <form action="{{ route('admin.studentList', $level) }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search LRN or Name..." 
                           class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none w-64 shadow-sm">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                </form>

                <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded shadow-sm border">
                    Total: <span class="font-bold text-blue-600">{{ $students->count() }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-6 py-3 text-sm font-semibold text-slate-600">LRN</th>
                <th class="px-6 py-3 text-sm font-semibold text-slate-600">First Name Middle Name Last Name</th>
                <th class="px-6 py-3 text-sm font-semibold text-slate-600">Level</th> 
                <th class="px-6 py-3 text-sm font-semibold text-slate-600">Status</th>
                <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y" x-data="{ expanded: false }">
            @forelse($students as $index => $student)
            <tr class="hover:bg-slate-50 transition" 
                x-show="expanded || {{ $index }} < 5" 
                x-transition>
                
                <td class="px-6 py-4 text-sm text-slate-700 font-mono font-bold">{{ $student->lrn }}</td>
                <td class="px-6 py-4 text-sm font-medium text-slate-800 capitalize">
                    {{ $student->fullname }}
                </td>
        
                <td class="px-6 py-4 text-sm">
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold uppercase">
                        {{ $student->level ?? 'N/A' }}
                    </span>
                </td>
        
                <td class="px-6 py-4 text-sm">
                    <span class="{{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2 py-1 rounded-full text-xs font-bold uppercase">
                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                
                <td class="px-6 py-4 text-right space-x-2">
                    <button onclick="openStudentArchiveModal('{{ $student->id }}', '{{ $student->fullname }}')" 
                            class="text-orange-600 hover:text-orange-900 bg-orange-50 p-2 rounded-lg transition">
                        <i class="fas fa-archive"></i>
                    </button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-slate-500 italic">No students found.</td>
                </tr>
            @endforelse
        
            @if($students->count() > 5)
            <tr>
                <td colspan="5" class="px-6 py-3 text-center bg-slate-50">
                    <button @click="expanded = !expanded" 
                            class="text-sm font-bold text-blue-600 hover:text-blue-800 transition flex items-center justify-center w-full">
                        <span x-text="expanded ? 'Show Less' : 'Show More ({{ $students->count() - 5 }} more)'"></span>
                        <i class="fas ml-2" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
    </div>

    <div id="studentArchiveModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm shadow-2xl text-center">
            <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-archive text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Archive Student?</h3>
            <p class="text-slate-500 text-sm mt-2">Are you sure you want to archive <span id="archiveStudentName" class="font-bold text-slate-800"></span>? You can restore this later from the Archive tab.</p>
            
            <form id="archiveStudentForm" method="POST" class="mt-6 flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeArchiveModal()" class="flex-1 py-2 bg-slate-100 rounded-lg font-semibold text-slate-600">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 shadow-md transition">Confirm</button>
            </form>
        </div>
    </div>

    <script>
        function openStudentArchiveModal(id, name) {
            const modal = document.getElementById('studentArchiveModal');
            const form = document.getElementById('archiveStudentForm');
            const nameSpan = document.getElementById('archiveStudentName');

            // Update form action - matches the deleteStudent function in your AdminController
            form.action = "/admin/students/delete/" + id;
            nameSpan.innerText = name;
            
            modal.classList.remove('hidden');
        }

        function closeArchiveModal() {
            document.getElementById('studentArchiveModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('studentArchiveModal');
            if (event.target == modal) {
                closeArchiveModal();
            }
        }
    </script>

</body>
</html>

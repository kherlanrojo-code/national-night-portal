<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Teacher List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <div class="w-64 bg-slate-800 min-h-screen text-white p-4">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-slate-700 pb-4">Admin Panel</h2>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Dashboard</a>
            <a href="#" class="block py-2.5 px-4 rounded bg-blue-600">Member's List</a>
            <a href="{{ route('admin.studentList', 'Junior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Junior High</a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Senior High</a>
            <a href="{{ route('admin.incomingGrades') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Incoming Grades</a>
            <a href="{{ route('admin.archive') }}" class="block py-2.5 px-4 rounded hover:bg-slate-700">Archive</a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Members Management</h1>
            
            <div class="flex items-center space-x-4">
                <form action="{{ route('admin.teachers') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Name..." 
                           class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none w-64 shadow-sm">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                </form>

                <button onclick="toggleModal('addTeacherModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Add Personel
                </button>
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
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600">Employee ID</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600">First Name Middle Name Last Name</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600">Position</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-6 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
    @foreach($teachers as $teacher)
    <tr class="hover:bg-slate-50 transition-colors">
        <td class="px-6 py-4 text-sm font-bold text-slate-800">
            {{ $teacher->employee_id }}
        </td>

        <td class="px-6 py-4 text-sm text-slate-700 capitalize">
            {{ $teacher->first_name }} {{ $teacher->middle_name }} {{ $teacher->last_name }}
        </td>

        <td class="px-6 py-4 text-sm text-slate-500 italic">
            {{ $teacher->position ?? 'Teacher' }}
        </td>

        <td class="px-6 py-4 text-sm">
            @if($teacher->is_active)
                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full uppercase">
                    Active
                </span>
            @else
                <span class="px-3 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full uppercase">
                    Inactive
                </span>
            @endif
        </td>

        <td class="px-6 py-4 text-right space-x-2">
            <button onclick="openEditModal('{{ $teacher->id }}', '{{ $teacher->employee_id }}', '{{ $teacher->first_name }}', '{{ $teacher->middle_name }}', '{{ $teacher->last_name }}', '{{ $teacher->position }}', '{{ $teacher->is_active }}')" 
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            
            <button onclick="openDeleteModal('{{ $teacher->id }}', '{{ $teacher->first_name }} {{ $teacher->last_name }}')" 
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
</tbody>

            </table>
        </div>
    </div>

    <div id="addTeacherModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4 text-slate-800 border-b pb-2">Register Teacher Identity</h3>
            <form action="{{ route('admin.storeTeacher') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Employee ID</label>
                    <input type="text" name="employee_id" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">First Name</label>
                        <input type="text" name="first_name" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                        <input type="text" name="middle_name" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none" placeholder="(Optional)">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Last Name</label>
                    <input type="text" name="last_name" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Position</label>
                    <select name="position" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="Teacher">Teacher</option>
                        <option value="Head Teacher">Head Teacher</option>
                        <option value="Admin">Admin</option>
                        <option value="Registrar">Registrar</option>
                    </select>
                </div>
                <div class="flex space-x-2 pt-4">
                    <button type="button" onclick="toggleModal('addTeacherModal')" class="flex-1 bg-slate-200 py-2 rounded font-semibold text-slate-700 hover:bg-slate-300 transition">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 shadow-md">Authorize Employee</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editTeacherModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4 text-slate-800 border-b pb-2 text-blue-600">Edit Teacher Details</h3>
            <form id="editForm" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Employee ID</label>
                    <input type="text" name="employee_id" id="edit_id" required class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                        <input type="text" name="middle_name" id="edit_middle_name" class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Last Name</label>
                    <input type="text" name="last_name" id="edit_last_name" required class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Position</label>
                        <select name="position" id="edit_position" class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="Teacher">Teacher</option>
                            <option value="Head Teacher">Head Teacher</option>
                            <option value="Admin">Admin</option>
                            <option value="Registrar">Registrar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status</label>
                        <select name="is_active" id="edit_active" class="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex space-x-2 pt-4">
                    <button type="button" onclick="toggleModal('editTeacherModal')" class="flex-1 bg-slate-100 py-2 rounded font-semibold text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm shadow-2xl text-center">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Archive Record?</h3>
            <p class="text-slate-500 text-sm mt-2">Are you sure you want to move <span id="deleteTeacherName" class="font-bold text-slate-800"></span> to the archive?</p>
            <form id="deleteForm" method="POST" class="mt-6 flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="toggleModal('deleteModal')" class="flex-1 py-2 bg-slate-100 rounded-lg font-semibold text-slate-600">No, Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 shadow-md">Yes, Archive</button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        function openEditModal(id, employeeId, firstName, middleName, lastName, position, active) {
            document.getElementById('editForm').action = "/admin/teachers/update/" + id;
            document.getElementById('edit_id').value = employeeId;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_middle_name').value = middleName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_position').value = position;
            document.getElementById('edit_active').value = active;
            toggleModal('editTeacherModal');
        }

        function openDeleteModal(id, name) {
            // FIXED: Added 's' to "teachers" to match your web.php route path
            document.getElementById('deleteForm').action = "/admin/teachers/delete/" + id; 
            document.getElementById('deleteTeacherName').innerText = name;
            toggleModal('deleteModal');
        }
    </script>

</body>
</html>
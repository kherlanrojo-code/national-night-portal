<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Manager - Night Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .modal-active { backdrop-filter: blur(8px); transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen font-sans">

    <div class="w-72 bg-slate-900 min-h-screen text-white p-6 sticky top-0 h-screen shadow-2xl">
        <div class="flex items-center space-x-3 mb-10 pb-6 border-b border-slate-800">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold tracking-tight">NIGHT PORTAL</h2>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest leading-none">Administrator</p>
            </div>
        </div>

        <nav class="space-y-1.5">
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-3 ml-4">Main Menu</p>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all font-semibold">
                <i class="fas fa-chart-pie mr-3 w-5 text-center"></i> Dashboard
            </a>
            
            <a href="{{ route('admin.subjects') }}" class="flex items-center py-3 px-4 rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20 transition-all font-semibold">
                <i class="fas fa-book mr-3 w-5 text-center"></i> Subject Manager
            </a>

            <a href="{{ route('admin.teachers') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                <i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i> Faculty Members
            </a>

            <div class="absolute bottom-8 left-6 right-6">
                <a href="{{ route('logout') }}" class="flex items-center justify-center py-3 px-4 rounded-xl bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all font-bold text-sm">
                    <i class="fas fa-power-off mr-2"></i> SIGN OUT
                </a>
            </div>
        </nav>
    </div>

    <div class="flex-1 p-6 lg:p-10 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 mb-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Subject Management</h2>
                        <p class="text-slate-500 mt-1 text-sm font-medium">Define curriculum learning areas</p>
                    </div>
                    
                    <form action="{{ route('admin.subjects.store') }}" method="POST" class="flex flex-wrap gap-3 w-full lg:w-auto">
                        @csrf
                        <input type="text" name="subject_code" placeholder="Code (e.g., 101)" required
                               class="bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                        
                        <input type="text" name="subject_name" placeholder="Subject Name" required
                               class="bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all lg:w-64">
                    
                        <div class="flex gap-2">
                            <input type="text" name="code" placeholder="Code (e.g., 101)" class="...">
                            <input type="text" name="name" placeholder="Subject Name" class="...">
                            
                            <select name="level" required class="border-slate-200 border p-3 rounded-xl text-sm">
                                <option value="" disabled selected>Select Level</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                            
                            <button type="submit" class="...">+ SAVE</button>
                        </div>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl text-sm font-black transition-all shadow-lg shadow-blue-500/25 active:scale-95">
                            <i class="fas fa-plus mr-2"></i> SAVE
                        </button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-xs font-bold flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                   <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-10 py-6 font-black">Code</th>
                            <th class="px-10 py-6 font-black">Learning Area</th>
                            <th class="px-10 py-6 font-black text-center">Level</th> <th class="px-10 py-6 font-black text-center">Status</th>
                            <th class="px-10 py-6 font-black text-right">Actions</th>
                        </tr>
                  </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($subjects as $subject)
                        <tr class="group hover:bg-slate-50/80 transition-all">
                            <td class="px-10 py-5">
                                <span class="text-blue-600 font-black text-xs bg-blue-50 px-4 py-2 rounded-xl border border-blue-100">
                                    {{ $subject->code }}
                                </span>
                            </td>
                            <td class="px-10 py-5 font-bold text-slate-700">{{ $subject->name }}</td>
                            <td class="px-10 py-5 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-600 text-[10px] font-black rounded-lg border border-green-200 uppercase">Active</span>
                            </td>
                            <td class="px-10 py-5 text-right">
                                <button onclick="confirmDelete({{ $subject->id }}, '{{ $subject->name }}')" 
                                        class="text-slate-400 hover:text-red-600 hover:bg-red-50 w-10 h-10 rounded-2xl inline-flex items-center justify-center transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-10 py-20 text-center text-slate-400 font-bold">No subjects defined yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 modal-active">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-center w-16 h-16 bg-red-50 text-red-500 rounded-2xl mb-6 mx-auto">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Confirm Deletion</h3>
                <p class="text-slate-500 text-sm mt-2 font-medium">
                    Are you sure you want to remove <span id="deleteSubjectName" class="text-red-600 font-bold"></span>? 
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-4 rounded-2xl bg-slate-100 text-slate-600 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-6 py-4 rounded-2xl bg-red-600 text-white font-black text-xs uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-500/20 transition-all">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('deleteSubjectName').innerText = name;
            document.getElementById('deleteForm').action = `/admin/subjects/${id}`;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) closeDeleteModal();
        }
    </script>
</body>
</html>

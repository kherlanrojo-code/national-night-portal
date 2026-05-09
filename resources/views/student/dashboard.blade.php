<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Night Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #imageModal { transition: opacity 0.3s ease; }
    </style>
</head>
<body class="bg-slate-50">

    <nav class="bg-slate-800 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wider">STUDENT PORTAL</h1>
            <a href="{{ route('logout') }}" class="text-sm bg-red-600 px-4 py-2 rounded hover:bg-red-700 transition">
                Logout
            </a>
        </div>
    </nav>

    <div class="container mx-auto mt-8 p-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200">
                    <div class="bg-blue-600 p-6 text-center">
                        <div class="relative group w-28 h-28 mx-auto mb-3">
                            @php
                                $profilePath = $student->profile_picture ? asset('profile_pics/' . $student->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($student->fullname) . '&background=random&color=fff&size=128';
                            @endphp
                            
                            <img id="profileImage" 
                                 src="{{ $profilePath }}" 
                                 class="w-28 h-28 rounded-full border-4 border-white object-cover shadow-lg transition group-hover:brightness-75"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->fullname) }}&background=64748b&color=fff'">
                            
                            <form action="{{ route('student.updatePicture', $student->lrn) }}" method="POST" enctype="multipart/form-data" id="profilePicForm" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                <label for="profile_pic" class="cursor-pointer bg-black/40 w-full h-full rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-camera text-2xl"></i>
                                </label>
                                <input type="file" name="profile_pic" id="profile_pic" class="hidden" accept="image/*" onchange="document.getElementById('profilePicForm').submit()">
                            </form>
                        </div>

                        <h2 class="text-white font-bold text-xl uppercase tracking-tight">{{ $student->fullname }}</h2>
                        
                        <button onclick="openModal('{{ $profilePath }}')" class="mt-3 text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-full transition border border-white/30">
                            <i class="fas fa-expand-alt mr-1"></i> View Profile Picture
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-widest">LRN Number</p>
                            <p class="text-slate-800 font-mono font-bold">{{ $student->lrn }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-widest">Grade Level</p>
                            <p class="text-slate-800 font-semibold">{{ $student->level }} High School</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-widest">Birthday</p>
                            <p class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($student->dob)->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Academic Grades</h3>
                            
                            <form action="{{ route('student.dashboard', $student->lrn) }}" method="GET" id="filterForm" class="mt-2">
                               <select name="semester" onchange="this.form.submit()" class="...">
                                    <option value="">All Semesters</option>
                                    <option value="1st Term" {{ request('semester') == '1st Term' ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2nd Term" {{ request('semester') == '2nd Term' ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="3rd Term" {{ request('semester') == '3rd Term' ? 'selected' : '' }}>3rd Semester</option>
                                </select>
                            </form>
                        </div>
                        
                        <a href="{{ route('student.print', ['lrn' => $student->lrn, 'semester' => request('semester')]) }}" target="_blank" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-900 transition flex items-center shadow-sm">
                            <i class="fas fa-print mr-2"></i> Print Report Card
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Subject</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Semester</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="grades-body">
                                @forelse($grades as $index => $grade)
                                <tr class="grade-row hover:bg-blue-50/50 transition {{ $index >= 5 ? 'hidden' : '' }}">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                        <span class="text-slate-400 font-normal mr-1">{{ $grade->subject_code }}</span> {{ $grade->subject }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ $grade->semester }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block w-16 py-1 rounded text-sm font-bold {{ $grade->grade < 75 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700 border border-green-200' }}">
                                            {{ number_format($grade->grade, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">No records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($grades) > 5)
                    <div class="p-4 bg-slate-50 text-center border-t border-slate-100">
                        <button id="toggle-btn" onclick="toggleGrades()" class="text-blue-600 font-bold text-xs uppercase tracking-widest hover:text-blue-800 transition flex items-center justify-center mx-auto">
                            <span id="btn-text">Show All Grades</span>
                            <i id="btn-icon" class="fas fa-chevron-down ml-2"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4" onclick="closeModal()">
        <div class="relative max-w-3xl w-full flex justify-center">
            <button class="absolute -top-10 right-0 text-white text-3xl hover:text-slate-300">&times;</button>
            <img id="modalImg" src="" class="max-h-[80vh] rounded-lg shadow-2xl border-4 border-white object-contain">
        </div>
    </div>

    <script>
        // Toggle Functionality
        let isExpanded = false;
        function toggleGrades() {
            const rows = document.querySelectorAll('.grade-row');
            const btnText = document.getElementById('btn-text');
            const btnIcon = document.getElementById('btn-icon');
            isExpanded = !isExpanded;

            rows.forEach((row, index) => {
                if (index >= 5) {
                    row.classList.toggle('hidden');
                }
            });

            if (isExpanded) {
                btnText.innerText = 'Show Less';
                btnIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                btnText.innerText = 'Show All Grades';
                btnIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }

        // Modal Functions
        function openModal(imgSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImg');
            modalImg.src = imgSrc;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") closeModal();
        });
    </script>
</body>
</html>
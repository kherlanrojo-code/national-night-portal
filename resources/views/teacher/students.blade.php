<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher - Student Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal-active { backdrop-filter: blur(4px); transition: all 0.3s ease; }
        .animate-bounce-short { animation: bounce 1s ease-in-out 1; }
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

    <div class="w-64 bg-indigo-900 text-white p-4 sticky top-0 h-screen">
        <h2 class="text-xl font-bold mb-8 text-center border-b border-indigo-700 pb-4 tracking-wide">Teacher Portal</h2>
        <nav class="space-y-2">
            <a href="{{ route('teacher.students', ['adviser_id' => request()->route('adviser_id')]) }}" 
               class="block py-2.5 px-4 rounded transition {{ !request()->route('level') ? 'bg-indigo-700' : 'hover:bg-indigo-800' }}">
               <i class="fas fa-users mr-2"></i> All Students
            </a>

            <div class="space-y-1">
                <button onclick="toggleSubmenu('juniorSubmenu')" class="w-full flex items-center justify-between py-2.5 px-4 rounded hover:bg-indigo-800 text-indigo-100 transition">
                    <span><i class="fas fa-child mr-2"></i> Junior High</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="juniorSubmenu" class="{{ str_contains(request()->route('level') ?? '', 'Grade 7') || str_contains(request()->route('level') ?? '', 'Grade 8') || str_contains(request()->route('level') ?? '', 'Grade 9') || str_contains(request()->route('level') ?? '', 'Grade 10') ? '' : 'hidden' }} pl-8 space-y-1">
                    @foreach(['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'] as $g)
                        <a href="{{ route('teacher.students', ['adviser_id' => request()->route('adviser_id'), 'level' => $g]) }}" 
                           class="block py-1.5 text-sm {{ request()->route('level') == $g ? 'text-white font-bold' : 'text-indigo-300 hover:text-white' }}">{{ $g }}</a>
                    @endforeach
                </div>
            </div>

            <div class="space-y-1">
                <button onclick="toggleSubmenu('seniorSubmenu')" class="w-full flex items-center justify-between py-2.5 px-4 rounded hover:bg-indigo-800 text-indigo-100 transition">
                    <span><i class="fas fa-user-graduate mr-2"></i> Senior High</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="seniorSubmenu" class="{{ str_contains(request()->route('level') ?? '', 'Grade 11') || str_contains(request()->route('level') ?? '', 'Grade 12') ? '' : 'hidden' }} pl-8 space-y-1">
                    @foreach(['Grade 11', 'Grade 12'] as $g)
                        <a href="{{ route('teacher.students', ['adviser_id' => request()->route('adviser_id'), 'level' => $g]) }}" 
                           class="block py-1.5 text-sm {{ request()->route('level') == $g ? 'text-white font-bold' : 'text-indigo-300 hover:text-white' }}">{{ $g }}</a>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-indigo-800">
                <a href="{{ route('logout') }}" class="block py-2.5 px-4 rounded hover:bg-red-800 text-red-200 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                    @if(request()->route('level'))
                        {{ request()->route('level') }} <span class="text-indigo-600 font-medium text-2xl">Students</span>
                    @else
                        My <span class="text-indigo-600 font-medium">Students</span>
                    @endif
                </h1>
                <p class="text-slate-500 text-sm mt-1">Classroom Management & Grading Portal</p>
            </div>
            
            <div class="flex items-center gap-3">
    <div class="flex bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
        <button onclick="setGradeFilter('all')" id="filterBtnAll" title="Show All"
                class="px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-indigo-600 text-white">
            ALL
        </button>
        <button onclick="setGradeFilter('no-grade')" id="filterBtnNoGrade" title="Students without grades"
                class="px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-400 hover:text-red-600">
            <i class="fas fa-times-circle"></i>
        </button>
        <button onclick="setGradeFilter('has-grade')" id="filterBtnHasGrade" title="Students with grades"
                class="px-3 py-1.5 rounded-md text-xs font-bold transition-all text-slate-400 hover:text-emerald-600">
            <i class="fas fa-check-circle"></i>
        </button>
    </div>

    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <i class="fas fa-search text-slate-400 text-sm"></i>
        </span>
        <input type="text" id="studentSearch" onkeyup="filterTable()" placeholder="Quick search..." 
               class="pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none shadow-sm w-48 text-sm transition-all">
    </div>
    
    <button onclick="toggleModal('addStudentModal')" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 shadow-md transition transform active:scale-95 flex items-center">
        <i class="fas fa-user-plus mr-2"></i> Enroll
    </button>
</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase">Total Enrolled</p>
                <p class="text-2xl font-bold text-slate-800">{{ $students->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-green-500">
                <p class="text-[10px] font-black text-slate-400 uppercase">Active Students</p>
                <p class="text-2xl font-bold text-slate-800">{{ $students->where('is_active', true)->count() }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase">Section</p>
                <p class="text-2xl font-bold text-indigo-600">A-2026</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-orange-500">
                <p class="text-[10px] font-black text-slate-400 uppercase">Assigned Level</p>
                <p class="text-xl font-bold text-slate-800 truncate">{{ request()->route('level') ?? 'All Grades' }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm animate-bounce-short">
                <i class="fas fa-check-circle mr-3"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <table class="w-full text-left" id="studentTable">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Student LRN</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">First Name Middle Name Last Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Level</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center tracking-widest">Edit Info</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($students as $student)
                    <tr class="hover:bg-indigo-50/40 transition-colors group">
                        <td class="px-6 py-4 text-sm text-slate-600 font-mono font-bold">{{ $student->lrn }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $student->fullname }}</span>
                        </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-[10px] font-black uppercase">{{ $student->level }}</span>
                            </td>
                        <td class="px-6 py-4">
                            @if($student->is_active == 1)
                                <span class="px-3 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">
                                    ACTIVE
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold text-orange-700 bg-orange-100 rounded-full">
                                    PENDING VERIFICATION
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                           <button onclick="openEditModal('{{ $student->id }}', '{{ $student->fullname }}', '{{ $student->lrn }}', '{{ $student->level }}')" 
                                class="text-blue-600 hover:text-blue-800 transition p-2">
                                <i class="fas fa-user-edit text-lg"></i>
                            </button>
                         </td>
                        <td class="px-6 py-4 text-center space-x-1">
                            <button onclick="openGradeModal('{{ $student->lrn }}', '{{ $student->fullname }}')" 
                                class="text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold text-[10px] uppercase border border-indigo-200 px-3 py-1.5 rounded-md transition-all shadow-sm">
                                <i class="fas fa-pen-nib mr-1"></i> Grade
                            </button>
                            <form action="{{ route('teacher.sendToAdmin', $student->lrn) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold text-[10px] uppercase border border-emerald-200 px-3 py-1.5 rounded-md transition-all shadow-sm">
                                    <i class="fas fa-check-double mr-1"></i> Finalize
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-search text-4xl mb-4 block opacity-20"></i>
                            <span class="italic">No student records found in this category.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addStudentModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 modal-active">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-extrabold mb-1 text-slate-800 uppercase tracking-tight">Student Enrollment</h3>
            <p class="text-slate-400 text-xs mb-6">Create a new student record for this academic year.</p>
            
            <form action="{{ route('teacher.storeStudent') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="adviser_id" value="{{ request()->route('adviser_id') }}">
                
                <div>
    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Student LRN (12 Digits)</label>
    <input type="text" 
           id="lrnInput" 
           name="lrn" 
           oninput="validateLRN()" 
           placeholder="000000000000" 
           required 
           class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-mono tracking-widest transition-all">
    <span id="lrnWarning" class="text-[10px] text-red-500 font-bold mt-1 hidden animate-pulse">
        <i class="fas fa-exclamation-circle"></i> LRN must be exactly 12 digits!
    </span>
</div>

                <div class="grid grid-cols-1 gap-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">First Name</label>
            <input type="text" name="first_name" placeholder="Juan" required 
                   class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
        </div>
        <div>
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Middle Name</label>
            <input type="text" name="middle_name" placeholder="Garcia" 
                   class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
        </div>
    </div>
    <div>
        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Last Name</label>
        <input type="text" name="last_name" placeholder="Dela Cruz" required 
               class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
    </div>
</div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Birth Date</label>
                        <input type="date" name="dob" required class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Category</label>
                        <select id="mainLevel" onchange="updateGradeOptions()" class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="">Select</option>
                            <option value="Junior">Junior High</option>
                            <option value="Senior">Senior High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Grade Level</label>
                    <select name="level" id="specificGrade" required class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">-- Select Category First --</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="toggleModal('addStudentModal')" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition">Enroll Now</button>
                </div>
            </form>
        </div>
    </div>

    <div id="gradeModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 modal-active">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-extrabold mb-2 text-slate-800 uppercase tracking-tight">Grade Encoding</h3>
        <p class="text-xs text-slate-400 mb-4">Student: <span id="targetName" class="text-indigo-600 font-bold uppercase tracking-wide"></span></p>

        <div class="mb-6 bg-slate-50/50 rounded-[2rem] p-6 border border-slate-200">
            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex items-center">
                <i class="fas fa-history mr-2"></i> RECORDED GRADES
            </h4>
            <div id="gradeHistoryContainer" class="space-y-1 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                <p class="text-[11px] text-slate-400 italic text-center">Loading grades...</p>
            </div>
        </div>

        <form action="{{ route('teacher.submitGrade') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="lrn" id="targetLRN">
            
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Select Subject</label>
                <select name="subject_id" id="subjectSelect" required class="w-full border-slate-200 border p-3 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="" disabled selected>-- Choose Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Numeric Grade</label>
                    <input type="number" name="grade" placeholder="0.00" step="0.01" min="60" max="100" required 
                           class="w-full border-slate-200 border p-3 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-lg font-black text-center">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Quarter/Period</label>
                    <select name="quarter" class="w-full border-slate-200 border p-3 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="1st Term">1st term</option>
                        <option value="2nd Term">2nd term</option>
                        <option value="3rd Term">3rd term</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('gradeModal')" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 shadow-lg transition">Save Grade</button>
            </div>
        </form>
    </div>
</div>

<div id="editStudentModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 modal-active">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-extrabold mb-1 text-slate-800 uppercase tracking-tight">Update Student Info</h3>
        <p class="text-slate-400 text-xs mb-6">Modify student details below.</p>
        
        <form action="{{ route('teacher.updateStudent') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" id="edit_student_id">
            
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Student LRN</label>
                <input type="text" name="lrn" id="edit_lrn" required 
                       class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm font-mono font-bold">
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Full Name</label>
                <input type="text" name="fullname" id="edit_fullname" required 
                       class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold uppercase">
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Grade Level</label>
                <select name="level" id="edit_level" required class="w-full border-slate-200 border p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold">
                    @foreach(['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $g)
                        <option value="{{ $g }}">{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg transition">Update Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    const allExistingGrades = @json($allGrades ?? []);
    let currentGradeFilter = 'all'; 

    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function toggleSubmenu(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

   function openGradeModal(lrn, name) {
            document.getElementById('targetLRN').value = lrn;
            document.getElementById('targetName').innerText = name;
            const historyContainer = document.getElementById('gradeHistoryContainer');
            
            // Filter grades for this specific student
            const studentGrades = allExistingGrades.filter(g => g.lrn == lrn);
            historyContainer.innerHTML = '';
            
            if (studentGrades.length > 0) {
                studentGrades.forEach(grade => {
                    const isSent = grade.is_submitted_to_admin == 1;
                    const statusIcon = isSent 
                        ? '<span class="text-[9px] text-emerald-500 font-bold ml-2"><i class="fas fa-check-double"></i> SENT</span>' 
                        : '<span class="text-[9px] text-orange-400 font-bold ml-2"><i class="fas fa-clock"></i> PENDING</span>';
        
                    // Use subject_code if available, otherwise try to match from the subjects list
                    const subjectsList = @json($subjects);
                    const matchingSubject = subjectsList.find(s => s.name === grade.subject);
                    const displayCode = grade.subject_code || (matchingSubject ? matchingSubject.code : 'N/A');
        
                    historyContainer.innerHTML += `
                        <div class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm mb-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-tighter mb-0.5">
                                        ${displayCode}
                                    </p>
                                    <h4 class="text-slate-800 font-bold capitalize text-sm leading-none">${grade.subject}</h4>
                                    <div class="flex items-center mt-2">
                                        <span class="text-[9px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md font-black uppercase tracking-wider">
                                            ${grade.semester || '1st Term'}
                                        </span>
                                        ${statusIcon}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[8px] text-slate-400 font-bold block uppercase tracking-tighter">Final Grade</span>
                                    <span class="text-emerald-600 font-black text-lg">${parseFloat(grade.grade).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                historyContainer.innerHTML = `
                    <div class="text-center py-6">
                        <i class="fas fa-folder-open text-slate-200 text-2xl mb-2 block"></i>
                        <p class="text-[11px] text-slate-400 italic font-medium">No grades recorded yet.</p>
                    </div>`;
            }
            toggleModal('gradeModal');
        }

    function updateGradeOptions() {
        const mainLevel = document.getElementById('mainLevel').value;
        const specificGrade = document.getElementById('specificGrade');
        specificGrade.innerHTML = '<option value="">-- Select Specific Grade --</option>';
        let options = (mainLevel === 'Junior') ? ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'] : (mainLevel === 'Senior' ? ['Grade 11', 'Grade 12'] : []);
        options.forEach(grade => {
            let opt = document.createElement('option');
            opt.value = grade; opt.innerHTML = grade;
            specificGrade.appendChild(opt);
        });
    }

    function setGradeFilter(filterType) {
        currentGradeFilter = filterType;
        const btns = {
            all: document.getElementById('filterBtnAll'),
            'no-grade': document.getElementById('filterBtnNoGrade'),
            'has-grade': document.getElementById('filterBtnHasGrade')
        };
        Object.values(btns).forEach(b => {
            b.classList.remove('bg-indigo-600', 'text-white', 'bg-red-600', 'bg-emerald-600');
            b.classList.add('text-slate-400');
        });
        if(filterType === 'all') btns.all.classList.add('bg-indigo-600', 'text-white');
        if(filterType === 'no-grade') btns['no-grade'].classList.add('bg-red-600', 'text-white');
        if(filterType === 'has-grade') btns['has-grade'].classList.add('bg-emerald-600', 'text-white');
        filterTable();
    }

    function filterTable() {
        const input = document.getElementById("studentSearch");
        const searchText = input.value.toUpperCase();
        const table = document.getElementById("studentTable");
        const tr = table.getElementsByTagName("tr");
        const lrnsWithGrades = [...new Set(allExistingGrades.map(g => String(g.lrn)))];

        for (let i = 1; i < tr.length; i++) {
            const tdLRN = tr[i].getElementsByTagName("td")[0];
            const tdName = tr[i].getElementsByTagName("td")[1];
            if (tdLRN && tdName) {
                const lrnValue = (tdLRN.textContent || tdLRN.innerText).trim();
                const nameValue = tdName.textContent || tdName.innerText;
                const hasGrade = lrnsWithGrades.includes(lrnValue);
                const matchesSearch = lrnValue.toUpperCase().indexOf(searchText) > -1 || 
                                    nameValue.toUpperCase().indexOf(searchText) > -1;
                let matchesStatus = true;
                if (currentGradeFilter === 'has-grade') matchesStatus = hasGrade;
                if (currentGradeFilter === 'no-grade') matchesStatus = !hasGrade;
                tr[i].style.display = (matchesSearch && matchesStatus) ? "" : "none";
            }
        }
    }

    // MOVED OUTSIDE filterTable() TO WORK PROPERLY
    function validateLRN() {
        const lrnInput = document.getElementById('lrnInput');
        const lrnWarning = document.getElementById('lrnWarning');
        let val = lrnInput.value.replace(/\D/g, ''); 
        if (val.length > 12) val = val.slice(0, 12);
        lrnInput.value = val;

        if (val.length === 0) {
            lrnInput.classList.remove('border-red-500', 'ring-red-500', 'border-emerald-500');
            lrnWarning.classList.add('hidden');
        } else if (val.length !== 12) {
            lrnInput.classList.add('border-red-500', 'ring-red-500');
            lrnWarning.classList.remove('hidden');
        } else {
            lrnInput.classList.remove('border-red-500', 'ring-red-500');
            lrnInput.classList.add('border-emerald-500');
            lrnWarning.classList.add('hidden');
        }
    }

    function openEditModal(id, name, lrn, level) {
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_fullname').value = name;
    document.getElementById('edit_lrn').value = lrn;
    document.getElementById('edit_level').value = level;
    
    document.getElementById('editStudentModal').classList.remove('hidden');
    document.getElementById('editStudentModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editStudentModal').classList.remove('flex');
        document.getElementById('editStudentModal').classList.add('hidden');
    }
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Night Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
        .status-pulse { animation: pulse-green 2s infinite; }
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
            <a href="{{ route('admin.dashboard') }}" class="flex items-center py-3 px-4 rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20 transition-all font-semibold">
                <i class="fas fa-chart-pie mr-3 w-5 text-center"></i> Dashboard
            </a>
            <a href="{{ route('admin.teachers') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                <i class="fas fa-chalkboard-teacher mr-3 w-5 text-center"></i> Faculty Members
            </a>

            <a href="{{ route('admin.monitoring') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                 <i class="fas fa-tasks mr-3 w-5 text-center"></i> Submission Tracker
            </a>

           

            <a href="{{ route('admin.subjects') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all font-semibold">
                <i class="fas fa-book mr-3 w-5 text-center"></i> Subject Manager
            </a>
            
            <a href="{{ route('admin.studentList', 'Junior') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                <i class="fas fa-shapes mr-3 w-5 text-center"></i> Junior High
            </a>
            <a href="{{ route('admin.studentList', 'Senior') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                <i class="fas fa-graduation-cap mr-3 w-5 text-center"></i> Senior High
            </a>
            
            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-8 mb-3 ml-4">System Actions</p>
            
            <button onclick="toggleSignatoryModal()" class="w-full flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all text-left">
                <i class="fas fa-pen-fancy mr-3 w-5 text-center"></i> Set Signatories
            </button>

            <a href="{{ route('admin.incomingGrades') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all relative">
                <i class="fas fa-file-signature mr-3 w-5 text-center"></i> Grade Requests
                @if($pendingGrades > 0)
                    <span class="absolute right-4 bg-orange-500 text-[10px] text-white font-bold px-2 py-0.5 rounded-full">{{ $pendingGrades }}</span>
                @endif
            </a>
            <a href="{{ route('admin.archive') }}" class="flex items-center py-3 px-4 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white transition-all">
                <i class="fas fa-box-archive mr-3 w-5 text-center"></i> Data Archive
            </a>
        </nav>

        <div class="absolute bottom-8 left-6 right-6">
            <a href="{{ route('logout') }}" class="flex items-center justify-center py-3 px-4 rounded-xl bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all font-bold text-sm">
                <i class="fas fa-power-off mr-2"></i> SIGN OUT
            </a>
        </div>
    </div>

    <div class="flex-1 p-10 overflow-y-auto">
        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl mb-6 shadow-sm flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Executive Dashboard</h1>
                <p class="text-slate-500 font-medium">Academic Year 2025-2026 | System Overview</p>
            </div>
            
            <div class="flex items-center space-x-3 bg-white px-4 py-2.5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="relative flex h-3 w-3">
                    <span class="status-pulse absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </div>
                <span class="text-xs font-bold text-slate-700 tracking-wide">SYSTEM LIVE</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-blue-500 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 group-hover:scale-110 transition-transform">
                        <i class="fas fa-id-badge text-xl"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-slate-900">{{ $totalTeachers }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Verified Faculty</p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group hover:border-indigo-500 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-friends text-xl"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-slate-900">{{ $totalStudents }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Enrolled Students</p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:border-emerald-500 transition-all">
                <p class="text-[10px] text-emerald-600 font-black uppercase mb-1">Junior Division</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold text-slate-800">{{ $totalStudents > 0 ? round(($juniorCount/$totalStudents)*100) : 0 }}%</h3>
                    <span class="text-xs font-bold text-slate-400">{{ $juniorCount }} Students</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalStudents > 0 ? ($juniorCount/$totalStudents)*100 : 0 }}%"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 group hover:border-purple-500 transition-all">
                <p class="text-[10px] text-purple-600 font-black uppercase mb-1">Senior Division</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold text-slate-800">{{ $totalStudents > 0 ? round(($seniorCount/$totalStudents)*100) : 0 }}%</h3>
                    <span class="text-xs font-bold text-slate-400">{{ $seniorCount }} Students</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $totalStudents > 0 ? ($seniorCount/$totalStudents)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Enrollment Distribution</h2>
                        <p class="text-sm text-slate-500">Breakdown by Grade Level</p>
                    </div>
                </div>
                <div id="enrollmentChart"></div>
            </div>

            <div class="bg-slate-900 p-8 rounded-[2rem] shadow-2xl text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-bell text-orange-400 mr-3"></i> Action Items
                </h2>
                <div class="space-y-6">
                    <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50">
                        <p class="text-[10px] text-orange-400 font-black uppercase tracking-tighter mb-2">Needs Approval</p>
                        <h4 class="font-bold text-lg leading-tight mb-2">{{ $pendingGrades }} Grading Batches Pending</h4>
                        <a href="{{ route('admin.incomingGrades') }}" class="block text-center py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-orange-500/20">
                            Review Now
                        </a>
                    </div>

                    <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50">
                        <p class="text-[10px] text-blue-400 font-black uppercase tracking-tighter mb-2">Compliance Check</p>
                        <h4 class="font-bold text-lg leading-tight mb-2">Monitor Faculty Submissions</h4>
                        <a href="{{ route('admin.monitoring') }}" class="block text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-500/20">
                            Open Tracker
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="signatoryModal" class="hidden fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 modal-active">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-slate-900 uppercase">Set Signatories</h3>
                <button onclick="toggleSignatoryModal()" class="text-slate-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="{{ route('admin.updateSignatories') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Registrar / Admin Name</label>
                    <input type="text" name="registrar_name" value="{{ $signatories->registrar ?? 'Not Set' }}" placeholder="Enter Full Name" required 
                           class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700">
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">School Head / Principal</label>
                    <input type="text" name="head_name" value="{{ $signatories->school_head ?? 'Not Set' }}" placeholder="Enter Full Name" required 
                           class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black text-sm hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all uppercase tracking-widest">
                        Apply to Grade Forms
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle Function for Modal
        function toggleSignatoryModal() {
            const modal = document.getElementById('signatoryModal');
            modal.classList.toggle('hidden');
        }

        // Chart Data
        var options = {
            series: [{
                name: 'Students',
                data: [{{ $juniorCount }}, {{ $seniorCount }}]
            }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: {
                bar: { borderRadius: 10, columnWidth: '40%', distributed: true }
            },
            colors: ['#10b981', '#a855f7'],
            dataLabels: { enabled: false },
            xaxis: {
                categories: ['Junior High', 'Senior High'],
                labels: { style: { colors: '#64748b', fontWeight: 600 } }
            },
            grid: { borderColor: '#f1f5f9' },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#enrollmentChart"), options);
        chart.render();
    </script>
</body>
</html>

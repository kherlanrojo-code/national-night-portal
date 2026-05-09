<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Night Portal - Welcome</title>
    @vite('resources/css/app.css')
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
    </style>
</head>
<body class="bg-gradient min-h-screen flex flex-col items-center justify-center text-white p-6">

    <div class="max-w-4xl text-center">
        <div class="mb-8 inline-block p-4 rounded-full bg-blue-600/20 border border-blue-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>

        <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight">
            NATIONAL <span class="text-blue-500">NIGHT PORTAL</span>
        </h1>
        
        <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl mx-auto">
            A secure digital gateway for Students and Teachers to manage academic records, 
            view grades, and track educational progress in real-time.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-lg transition-all transform hover:scale-105 shadow-lg shadow-blue-900/20">
                Proceed to Login
            </a>
            
            
        </div>

        <footer class="mt-20 text-slate-500 text-sm">
            &copy; {{ date('2026') }} National Night Portal System. All rights reserved.
        </footer>
    </div>

</body>
</html>
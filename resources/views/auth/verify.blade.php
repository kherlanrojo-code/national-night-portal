<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Credentials - Night Portal</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-2xl font-bold mb-2 text-center text-slate-800">Verify Identity</h2>
        <p class="text-slate-500 text-sm text-center mb-6">Enter your ID and Full Name as recorded by the Admin.</p>

        <form action="{{ route('verify.identity') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-1">ID Number (LRN or Employee ID)</label>
                <input type="text" name="identifier" class="w-full border rounded p-2" placeholder="e.g. 12345" required>
            </div>
            <div class="grid grid-cols-2 gap-2">
    <div class="grid grid-cols-2 gap-2">

    <div>
        <label class="block text-xs font-bold mb-1 uppercase text-slate-600">First Name</label>
        <input type="text" name="first_name" class="w-full border rounded p-2 text-sm" placeholder="Kerlan" required>
    </div>
   
</div>
<div>
    <label class="block text-xs font-bold mb-1 uppercase text-slate-600">Middle Name</label>
    <input type="text" name="middle_name" class="w-full border rounded p-2 text-sm" placeholder="Jorge">
</div>
    <div>
        <label class="block text-xs font-bold mb-1 uppercase text-slate-600">Last Name</label>
        <input type="text" name="last_name" class="w-full border rounded p-2 text-sm" placeholder="Rojo" required>
    </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700 transition">
                Check Record
            </button>

            @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative mb-4 text-sm text-center">
        {{ session('error') }}
    </div>
@endif
            <a href="{{ route('login') }}" class="block text-center text-sm text-blue-600 mt-2">Back to Login</a>
        </form>
    </div>
</body>
</html>
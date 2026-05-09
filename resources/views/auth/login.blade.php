<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - National Night Portal</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-slate-800">COMPOSTELA NHS EVENING CLASS</h2>
        
        @if(session('error'))
            <p class="text-red-500 bg-red-50 p-2 rounded text-sm mb-4 border border-red-200 text-center">
                {{ session('error') }}
            </p>
        @endif

        @if(session('success'))
            <p class="text-green-600 bg-green-50 p-2 rounded text-sm mb-4 border border-green-200 text-center">
                {{ session('success') }}
            </p>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-1 text-slate-700">Username</label>
                <input type="text" name="username" class="w-full border rounded p-2 focus:outline-blue-500 text-slate-800" required>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="text-sm font-bold text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Forgot Password?</a>
                </div>
                <input type="password" name="password" class="w-full border rounded p-2 focus:outline-blue-500 text-slate-800" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition shadow-md">
                Login
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500 mb-2">New Teacher or Student?</p>
            <a href="{{ route('verify.page') }}" class="inline-block w-full border border-blue-600 text-blue-600 py-2 rounded font-bold hover:bg-blue-50 transition">
                Verify Credentials
            </a>
        </div>
    </div>

</body>
</html>
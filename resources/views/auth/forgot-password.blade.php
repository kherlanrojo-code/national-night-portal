<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - National Night Portal</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-2xl font-bold mb-4 text-center text-slate-800">RESET PASSWORD</h2>
        <p class="text-sm text-slate-500 mb-6 text-center">Verify your identity using your ID Number.</p>

        @if(session('error'))
            <p class="text-red-500 bg-red-50 p-2 rounded text-sm mb-4 border border-red-200 text-center">
                {{ session('error') }}
            </p>
        @endif

        <form action="{{ route('password.reset.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-1">Username</label>
                <input type="text" name="username" class="w-full border rounded p-2 focus:outline-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">ID Number (LRN / Employee ID)</label>
                <input type="text" name="identifier" class="w-full border rounded p-2 focus:outline-blue-500" required placeholder="e.g. 234567">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">New Password</label>
                <input type="password" name="password" class="w-full border rounded p-2 focus:outline-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2 focus:outline-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition">
                Reset Password
            </button>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
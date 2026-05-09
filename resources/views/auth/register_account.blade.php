<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Account - Night Portal</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-2xl font-bold text-center text-slate-800">Setup Account</h2>
        <p class="text-center text-sm text-blue-600 mb-6 font-bold uppercase">{{ $role }}: {{ $name }}</p>

        <form action="{{ route('register.account') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="identifier" value="{{ $id }}">
            <input type="hidden" name="role" value="{{ $role }}">

            <div>
                <label class="block text-sm font-bold">Choose Username</label>
                <input type="text" name="username" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-bold">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-bold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">
                Finalize Registration
            </button>
        </form>
    </div>
</body>
</html>
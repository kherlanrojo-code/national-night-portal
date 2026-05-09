<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Night Portal - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-slate-800 mb-6">COMPOSTELA NHS EVENING CLASS</h2>

        <div class="flex justify-around mb-6 bg-slate-100 p-1 rounded-md">
            <button onclick="setRole('student')" id="btn-student" class="flex-1 py-2 rounded text-sm font-semibold bg-blue-600 text-white">Student</button>
            <button onclick="setRole('teacher')" id="btn-teacher" class="flex-1 py-2 rounded text-sm font-semibold text-slate-600">Teacher</button>
            <button onclick="setRole('admin')" id="btn-admin" class="flex-1 py-2 rounded text-sm font-semibold text-slate-600">Admin</button>
        </div>

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="role" id="login-role" value="student">
            
            <div>
                <label class="block text-sm font-medium text-slate-700">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded-md hover:bg-slate-700 transition">Login</button>
        </form>

        <hr class="my-6">

        <div class="text-center">
            <p class="text-sm text-slate-600 mb-2">First time here? Activate your account.</p>
            <button onclick="toggleModal('verifyModal')" class="text-blue-600 font-bold hover:underline">Verify Credentials</button>
        </div>
    </div>

    <div id="verifyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold mb-4">Verify Identity</h3>
            <form action="{{ route('student.verify') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="lrn" placeholder="LRN or Employee ID" required class="w-full border p-2 rounded">
                <input type="text" name="fullname" placeholder="Full Name" required class="w-full border p-2 rounded">
                <input type="date" name="dob" required class="w-full border p-2 rounded">
                
                <div class="flex space-x-2">
                    <button type="button" onclick="toggleModal('verifyModal')" class="flex-1 bg-slate-200 py-2 rounded">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded">Verify</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setRole(role) {
            document.getElementById('login-role').value = role;
            document.querySelectorAll('.flex-1').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-slate-600');
            });
            document.getElementById('btn-' + role).classList.add('bg-blue-600', 'text-white');
            document.getElementById('btn-' + role).classList.remove('text-slate-600');
        }

        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }
    </script>

    @if(session('success'))
        <script>alert("{{ session('success') }}");</script>
    @endif
    @if(session('error'))
        <script>alert("{{ session('error') }}");</script>
    @endif

</body>
</html>
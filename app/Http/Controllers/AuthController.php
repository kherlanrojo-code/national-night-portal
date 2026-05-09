<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TeacherIdentity;
use App\Models\StudentIdentity;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // 1. THE MASTER KEY (Hardcoded Bypass)
    // This ensures that even if the database is empty or messed up,
    // the "Admin" with "supersecure" can always get in.
    if ($request->username === 'Admin' && $request->password === 'supersecure') {
        $user = User::where('role', 'admin')->first();
        if ($user) {
            Auth::login($user);
            session(['role' => 'admin']);
            return redirect()->route('admin.dashboard');
        }
    }

    // 2. THE DATABASE CHECK (For Added Admins, Teachers, and Students)
    // This checks the 'users' table for any account created via the system.
    if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
        $user = Auth::user();
        $role = strtolower($user->role);

        // Crucial: Set the session role for the VerifyRole middleware
        session(['role' => $role]);

        // Redirect based on role
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'teacher') {
            return redirect()->route('teacher.students', ['adviser_id' => $user->identifier]);
        }

        if ($role === 'student') {
            return redirect()->route('student.dashboard', ['lrn' => $user->identifier]);
        }
    }

    // 3. FAIL STATE
    return back()->with('error', 'Invalid username or password.');
}

    public function showResetForm()
    {
        return view('auth.forgot-password');
    }

    public function handleReset(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'identifier' => 'required',
            'password' => 'required|min:4|confirmed',
        ]);

        $user = User::where('username', $request->username)
                    ->where('identifier', $request->identifier)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Records do not match.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully!');
    }

    public function verifyIdentity(Request $request)
    {
        $identifier = $request->identifier;
        $firstName  = strtolower(trim($request->first_name));
        $lastName   = strtolower(trim($request->last_name));

        // 1. Check Personnel (Teacher or Admin)
        $teacher = TeacherIdentity::where('employee_id', $identifier)->first();
        if ($teacher) {
            // FIX: Defining the missing variables
            $dbFirst = strtolower($teacher->first_name);
            $dbLast  = strtolower($teacher->last_name);

            if ($firstName === $dbFirst && $lastName === $dbLast) {
                return redirect()->route('register.step2', [
                    'id' => $teacher->id, 
                    'type' => strtolower($teacher->position), // This makes sure Admin stays Admin
                    'identifier' => $identifier 
                ]);
            }
        }

        // 2. Check Student
        $student = StudentIdentity::where('lrn', $identifier)->first();
        if ($student) {
            $dbFullname = strtolower($student->fullname);
            if (str_contains($dbFullname, $firstName) && str_contains($dbFullname, $lastName)) {
                return redirect()->route('register.step2', [
                    'id' => $student->id, 
                    'type' => 'student', 
                    'identifier' => $identifier 
                ]);
            }
        }

        return back()->with('error', 'No record found matching these credentials.');
    }

    public function registerAccount(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:4|confirmed',
            'role'     => 'required',
            'identifier' => 'required',
        ]);

        User::create([
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => strtolower($request->role), 
            'identifier' => $request->identifier,       
        ]);

        $role = strtolower($request->role);
        
        if ($role === 'teacher' || $role === 'admin') {
            TeacherIdentity::where('employee_id', $request->identifier)->update(['is_active' => true]);
        } else {
            StudentIdentity::where('lrn', $request->identifier)->update(['is_active' => true]);
        }

        return redirect()->route('login')->with('success', 'Account activated!');
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect('/login')->with('success', 'You have been logged out.');
    }
}

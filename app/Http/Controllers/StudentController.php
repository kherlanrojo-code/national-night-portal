<?php

namespace App\Http\Controllers;

use App\Models\StudentIdentity;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class StudentController extends Controller
{
    public function verifyIdentity(Request $request)
    {
        $request->validate([
            'lrn' => 'required',
            'fullname' => 'required',
            'dob' => 'required|date'
        ]);

        $identity = StudentIdentity::where('lrn', $request->lrn)
            ->where('fullname', $request->fullname)
            ->where('dob', $request->dob)
            ->first();

        if ($identity) {
            return view('auth.create_account', ['lrn' => $identity->lrn, 'role' => 'student']);
        }

        return redirect()->back()->with('error', 'Identity match failed. Please check your LRN and details.');
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'lrn' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        User::create([
            'name' => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'identifier' => $request->lrn 
        ]);

        StudentIdentity::where('lrn', $request->lrn)->update(['is_active' => true]);

        return redirect('/login')->with('success', 'Account created! You can now login.');
    }

    public function updateProfilePicture(Request $request, $lrn)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $student = StudentIdentity::where('lrn', $lrn)->first();

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $name = $lrn . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('profile_pics');
            
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            $image->move($path, $name);
            $student->update(['profile_picture' => $name]);
        }

        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }

    public function dashboard(Request $request, $lrn)
    {
        $student = StudentIdentity::where('lrn', $lrn)->first();
        if (!$student) { abort(404); }

        // This syntax forces PostgreSQL to treat it as a boolean
        $query = Grade::where('lrn', $lrn)->whereRaw('is_published = true');

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $grades = $query->get();
        $gpa = $grades->count() > 0 ? $grades->avg('grade') : 0;

        $signatories = (object)[
            'registrar' => DB::table('settings')->where('key', 'registrar')->value('value'),
            'school_head' => DB::table('settings')->where('key', 'school_head')->value('value')
        ];

        return view('student.dashboard', compact('student', 'grades', 'gpa', 'signatories'));
    }

public function printGrades(Request $request, $lrn)
{
    $student = \App\Models\StudentIdentity::where('lrn', $lrn)->first();
    if (!$student) { abort(404); }

    $query = \App\Models\Grade::where('grades.lrn', $lrn)
        ->whereRaw('grades.is_published = true')
        // Join on name AND level, but use a LEFT JOIN so the grade shows even if the subject code is missing
        ->leftJoin('subjects', function($join) use ($student) {
            $join->on('grades.subject', '=', 'subjects.name')
                 ->where('subjects.level', '=', $student->level);
        })
        // Select everything from grades, and the code from subjects (aliased to avoid conflict)
        ->select('grades.*', 'subjects.code as subject_code_from_table');

    if ($request->filled('semester')) {
        $query->where('grades.semester', $request->semester);
    }

    // IMPORTANT: Remove ->unique('subject') so that 1st Term Math AND 3rd Term Math both show up
    $grades = $query->orderBy('grades.semester', 'asc')->get();

    $gpa = $grades->count() > 0 ? $grades->avg('grade') : 0;

    $signatories = (object)[
        'registrar' => \DB::table('settings')->where('key', 'registrar')->value('value'),
        'school_head' => \DB::table('settings')->where('key', 'school_head')->value('value')
    ];

    return view('student.print', compact('student', 'grades', 'gpa', 'signatories'));
}
}

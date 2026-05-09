<?php

namespace App\Http\Controllers;

use App\Models\StudentIdentity;
use App\Models\Grade;
use App\Models\TeacherIdentity;
use App\Models\Subject; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function studentList($adviser_id, $level = null)
    {
        $teacher = TeacherIdentity::where('employee_id', $adviser_id)->first();

        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        $query = StudentIdentity::where('adviser_id', $teacher->id);

        if ($level) {
            $query->where('level', $level);
        }

        $students = $query->get();
        
        $allGrades = Grade::whereIn('lrn', $students->pluck('lrn'))
            ->orderBy('created_at', 'desc')
            ->get();

        $subjects = Subject::orderBy('name', 'asc')->get();

        return view('teacher.students', compact('students', 'subjects', 'allGrades'));
    }

    public function storeStudent(Request $request) {
        $request->validate([
            'lrn' => 'required|digits:12|unique:student_identities,lrn',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'dob' => 'required|date',
            'level' => 'required',
            'adviser_id' => 'required' 
        ]);

        $teacher = TeacherIdentity::where('employee_id', $request->adviser_id)->first();

        if (!$teacher) {
            return back()->with('error', 'Teacher record not found.');
        }

        StudentIdentity::create([
            'lrn' => $request->lrn,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name ?? '', 
            'last_name' => $request->last_name,
            'fullname' => trim("{$request->first_name} {$request->middle_name} {$request->last_name}"),
            'dob' => $request->dob,
            'level' => $request->level,
            'adviser_id' => $teacher->id, 
            'is_active' => true, 
        ]);

        return back()->with('success', 'Student enrolled successfully!');
    }

    public function submitGrade(Request $request)
    {
        $request->validate([
            'lrn' => 'required',
            'subject_id' => 'required',
            'grade' => 'required|numeric',
            'quarter' => 'required'
        ]);

        $subject = \App\Models\Subject::find($request->subject_id);
        if (!$subject) {
            return redirect()->back()->with('error', 'Subject not found.');
        }

        $exists = \App\Models\Grade::where('lrn', $request->lrn)
            ->where('subject', $subject->name) 
            ->where('semester', $request->quarter)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Grade for ' . $subject->name . ' in ' . $request->quarter . ' is already recorded.');
        }

        \App\Models\Grade::create([
            'lrn' => $request->lrn,
            'subject' => $subject->name, 
            'grade' => $request->grade,
            'semester' => $request->quarter,
            'is_submitted_to_admin' => DB::raw('false'), 
            'is_published' => DB::raw('false')
        ]);

        return redirect()->back()->with('success', 'Grade recorded for ' . $request->quarter);
    }

   public function sendToAdmin($lrn)
{
    // Use DB::raw('false') for the query and DB::raw for the updates
    \App\Models\Grade::where('lrn', $lrn)
        ->where('is_submitted_to_admin', DB::raw('false')) 
        ->update([
            'is_submitted_to_admin' => DB::raw('true'),
            'is_published' => DB::raw('false')
        ]);

    return redirect()->back()->with('success', 'Grades sent! Check Admin Grade Requests.');
}

    public function updateStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:student_identities,id',
            'lrn' => 'required|digits:12',
            'fullname' => 'required|string|max:255',
            'level' => 'required|string'
        ]);

        $student = StudentIdentity::findOrFail($request->student_id);
        
        $student->update([
            'lrn' => $request->lrn,
            'fullname' => strtoupper($request->fullname), 
            'level' => $request->level,
        ]);

        return back()->with('success', 'Student information updated successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TeacherIdentity;
use App\Models\StudentIdentity;
use App\Models\Grade;
use App\Models\Subject; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Helper to get signatories from the database
     */
   /**
     * Helper to get signatories from the database (with Null Safety)
     */
    private function getSignatories()
    {
        // The ?? 'Not Set' prevents the Error 500 if the database is empty
        return (object)[
            'registrar' => DB::table('settings')->where('key', 'registrar')->value('value') ?? 'Not Set',
            'school_head' => DB::table('settings')->where('key', 'school_head')->value('value') ?? 'Not Set'
        ];
    }

    public function dashboard()
    {
        // 1. Basic Counts
        $totalTeachers = TeacherIdentity::count();
        $totalStudents = StudentIdentity::count();
        
        // 2. Count students by group using specific Grade labels
        $juniorCount = StudentIdentity::whereIn('level', ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'])->count();
        $seniorCount = StudentIdentity::whereIn('level', ['Grade 11', 'Grade 12'])->count();
        
    $pendingGrades = Grade::whereRaw('is_submitted_to_admin::text = ?', ['true'])
                      ->whereRaw('is_published::text = ?', ['false'])
                      ->distinct('lrn')
                      ->count();
        
        $subjects = Subject::all(); 

        // 5. Fetch current Signatories for the dashboard modal
        $signatories = $this->getSignatories();

        // 6. Top 5 Performing Students
     $topStudents = Grade::whereRaw('is_published::text = ?', ['true'])
        ->select('lrn', DB::raw('AVG(grade) as average'))
            ->groupBy('lrn')
            ->orderBy('average', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                $student = StudentIdentity::where('lrn', $item->lrn)->first();
                $item->fullname = $student ? $student->fullname : 'Unknown Student';
                $item->level = $student ? $student->level : 'N/A';
                return $item;
            });

        return view('admin.dashboard', compact(
            'totalTeachers', 'totalStudents', 'pendingGrades', 
            'juniorCount', 'seniorCount', 'topStudents', 'subjects', 'signatories'
        ));
    }
    /**
     * Store a new subject with Code and Name
     */
    /**
     * Store a new subject with Code and Name (Validated)
     */
    public function storeSubject(Request $request) 
    {
        // This part prevents the Error 500 by checking the DB first
        $request->validate([
            'subject_code' => 'required|unique:subjects,code', 
            'subject_name' => 'required|string',
        ]);

        Subject::create([
            'code' => $request->subject_code,
            'name' => $request->subject_name,
        ]);

        return back()->with('success', 'New subject added to the curriculum!');
    }

    public function deleteSubject($id)
{
    \App\Models\Subject::findOrFail($id)->delete();
    return redirect()->back()->with('success', 'Subject removed successfully!');
}

    public function showSubjects()
{
    // Fetch the subjects from the DB to show them in the table
    $subjects = Subject::all(); 
    
    // Return the new separate blade file
    return view('admin.subjects', compact('subjects'));
}

    public function teacherList(Request $request)
    {
        $search = $request->input('search');

        $teachers = TeacherIdentity::when($search, function($query) use ($search) {
            return $query->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('employee_id', 'like', "%{$search}%")
                         ->orWhere('position', 'like', "%{$search}%");
        })->get();

        return view('admin.teachers', compact('teachers'));
    }

    /**
     * Authorize a new teacher with separated names and position
     */
        public function storeTeacher(Request $request)
            {
                $request->validate([
                    'employee_id' => 'required|unique:teacher_identities',
                    'first_name' => 'required|string|max:255',
                    'last_name'  => 'required|string|max:255',
                    'position'   => 'required|string',
                ]);
            
                // Create the full name string
                $fullname = trim($request->first_name . ' ' . ($request->middle_name ?? '') . ' ' . $request->last_name);
            
                TeacherIdentity::create([
                    'employee_id' => $request->employee_id,
                    'fullname'    => $fullname, // Add this line!
                    'first_name'  => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name'   => $request->last_name,
                    'position'    => $request->position,
                    'is_active'   => DB::raw('false'),
                ]);
            
                return redirect()->back()->with('success', 'Teacher identity authorized successfully!');
            }

    /**
     * Update Teacher Details (Edit Function)
     */
    public function updateTeacher(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|unique:teacher_identities,employee_id,' . $id,
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'position'    => 'required|string',
            'is_active'   => 'required|boolean'
        ]);

        $teacher = TeacherIdentity::findOrFail($id);
        
        $teacher->update([
            'employee_id' => $request->employee_id,
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'position'    => $request->position,
            'is_active'   => $request->is_active == '1' || $request->is_active == true ? DB::raw('true') : DB::raw('false'),
        ]);
        return redirect()->back()->with('success', 'Teacher record updated successfully!');
    }

    /**
     * Move Teacher to Archive (Soft Delete)
     */
    public function deleteTeacher($id)
{
    $teacher = TeacherIdentity::findOrFail($id);
    
    // Find the associated user using the Employee ID
    \App\Models\User::where('identifier', $teacher->employee_id)->delete();

    $teacher->delete();

    return redirect()->back()->with('success', 'Teacher and login account moved to archive.');
}
    /**
     * View Student Masterlist (Junior or Senior High)
     */
    public function studentMasterlist(Request $request, $level)
    {
        $search = $request->input('search');

        $gradeGroup = ($level === 'Junior') 
            ? ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'] 
            : ['Grade 11', 'Grade 12'];

        $students = StudentIdentity::whereIn('level', $gradeGroup)
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('fullname', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%");
                });
            })->get();

        return view('admin.student_list', compact('students', 'level'));
    }

    /**
     * Archive Student
     */
    public function deleteStudent($id)
{
    $student = StudentIdentity::findOrFail($id);
    
    // Find the associated user in the 'users' table using the LRN
    \App\Models\User::where('identifier', $student->lrn)->delete();

    // Soft delete the identity
    $student->delete();

    return redirect()->back()->with('success', 'Student and login account moved to archive.');
}
    public function archive()
    {
        $archivedStudents = StudentIdentity::onlyTrashed()->get();
        $archivedTeachers = TeacherIdentity::onlyTrashed()->get();
        
        return view('admin.archive', compact('archivedStudents', 'archivedTeachers'));
    }

    /**
     * Restore Records
     */
    public function restoreStudent($id)
    {
        StudentIdentity::withTrashed()->where('id', $id)->restore();
        return redirect()->back()->with('success', 'Student record restored!');
    }

    public function restoreTeacher($id)
    {
        TeacherIdentity::withTrashed()->where('id', $id)->restore();
        return redirect()->back()->with('success', 'Teacher record restored!');
    }

    public function forceDeleteTeacher($id)
    {
        TeacherIdentity::withTrashed()->where('id', $id)->forceDelete();
        return redirect()->back()->with('success', 'Permanently deleted.');
    }

    /**
     * Incoming Grades for Admin Approval
     */
  public function incomingGrades()
{
    // REMOVED ->with('subject') to stop the "RelationNotFoundException"
    $incomingGrades = \App\Models\Grade::whereRaw('is_submitted_to_admin::text = ?', ['true'])
        ->whereRaw('is_published::text = ?', ['false'])
        ->get()
        ->groupBy('lrn');

    return view('admin.incoming_grades', compact('incomingGrades'));
}

    public function forwardToStudent($lrn)
    {
        // FIX: Use DB::raw for both the WHERE clause and the UPDATE values
        \App\Models\Grade::where('lrn', $lrn)
            ->where('is_submitted_to_admin', DB::raw('true'))
            ->where('is_published', DB::raw('false'))
            ->update(['is_published' => DB::raw('true')]);

        return redirect()->back()->with('success', 'Grades published to student portal!');
    }
    /**
     * Generate PDF/Print Report
     */
    public function generateReport($lrn)
{
    $student = StudentIdentity::where('lrn', $lrn)->firstOrFail();
    $grades = Grade::where('lrn', $lrn)
                   ->where('is_published', true) // Changed from 1
                   ->get();
    
    $signatories = $this->getSignatories();

    return view('admin.reports.student_grade_pdf', compact('student', 'grades', 'signatories'));
}

    public function forceDeleteStudent($id)
    {
        StudentIdentity::withTrashed()->where('id', $id)->forceDelete();
        return redirect()->back()->with('success', 'Student record deleted permanently.');
    }

    /**
     * Update and Save Signatories to Database
     */
    public function updateSignatories(Request $request) {
        $request->validate([
            'registrar_name' => 'required|string',
            'head_name' => 'required|string',
        ]);

        // Saves to the settings table using updateOrInsert
        DB::table('settings')->updateOrInsert(
            ['key' => 'registrar'],
            ['value' => $request->registrar_name, 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'school_head'],
            ['value' => $request->head_name, 'updated_at' => now()]
        );

        return back()->with('success', 'Signatories updated successfully for all grade forms!');
    }
    
public function monitoring(Request $request)
{
    // 1. Force lowercase to prevent "1st Term" vs "1st term" issues
    $term = strtolower($request->get('term', '1st term'));
    
    // 2. Logic: 6 subjects per student
    $subjectsPerStudent = 6; 

    $teachers = \App\Models\TeacherIdentity::where('position', 'Teacher')
        ->get()
        ->map(function($teacher) use ($term, $subjectsPerStudent) {
            
            // 3. Convert to array to ensure the SQL 'IN' clause works on Live
            $studentIds = \App\Models\StudentIdentity::where('adviser_id', $teacher->id)->pluck('lrn')->toArray();
            $studentCount = count($studentIds);

            // Goal calculation
            $totalExpectedGrades = $studentCount * $subjectsPerStudent;

            if ($studentCount > 0) {
                // FIX: Use string-based whereRaw for booleans to avoid PostgreSQL operator errors
                $sentGrades = \App\Models\Grade::whereIn('lrn', $studentIds)
                    ->whereRaw('LOWER(semester) = ?', [$term])
                    ->whereRaw('is_submitted_to_admin::text = ?', ['true'])
                    ->count();

                $savedGrades = \App\Models\Grade::whereIn('lrn', $studentIds)
                    ->whereRaw('LOWER(semester) = ?', [$term])
                    ->whereRaw('is_submitted_to_admin::text = ?', ['false'])
                    ->count();
            } else {
                $sentGrades = 0;
                $savedGrades = 0;
            }

            $teacher->expected_total = $totalExpectedGrades;
            $teacher->actual_sent = $sentGrades;
            $teacher->has_drafts = ($savedGrades > 0);

            return $teacher;
        });

    return view('admin.monitoring', compact('teachers', 'term'));
}
}

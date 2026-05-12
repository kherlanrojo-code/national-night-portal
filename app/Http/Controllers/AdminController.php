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
        
  $pendingGrades = Grade::whereRaw('is_submitted_to_admin = CAST(? AS BOOLEAN)', [true])
                  ->whereRaw('is_published = CAST(? AS BOOLEAN)', [false])
                  ->distinct('lrn')
                  ->count('lrn');

        $subjects = Subject::all(); 

        // 5. Fetch current Signatories for the dashboard modal
        $signatories = $this->getSignatories();

        // 6. Top 5 Performing Students
    $topStudents = Grade::whereRaw('is_published = CAST(? AS BOOLEAN)', [true])
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
    // Validate that 'level' is being sent from the form
    $request->validate([
        'code' => 'required',
        'name' => 'required',
        'level' => 'required', 
    ]);

    // Save the subject with the selected Grade Level
   Subject::create([
    'code' => $request->code,
    'name' => $request->name,
    'level' => $request->level,
    // 'status' => 'ACTIVE', <--- REMOVE OR COMMENT OUT THIS LINE
]);

    return back()->with('success', 'Subject added successfully!');
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
    // 1. Find and restore the identity
    $student = StudentIdentity::withTrashed()->findOrFail($id);
    $student->restore();

    // 2. IMPORTANT: Flip the status back to Active so they can log in
    $student->update(['is_active' => true]);

    // 3. Optional: If you also soft-deleted the user login, restore it here
    \App\Models\User::withTrashed()->where('identifier', $student->lrn)->restore();

    return redirect()->back()->with('success', 'Student record and portal access restored!');
}
    /**
     * Incoming Grades for Admin Approval
     */
  public function incomingGrades()
{
    // REMOVED ->with('subject') to stop the "RelationNotFoundException"
    $incomingGrades = \App\Models\Grade::join('student_identities', 'grades.lrn', '=', 'student_identities.lrn')
    ->whereRaw('grades.is_submitted_to_admin::text = ?', ['true'])
    ->whereRaw('grades.is_published::text = ?', ['false'])
    ->whereColumn('grades.level', 'student_identities.level') // The critical fix
    ->select('grades.*') // Ensure you only select grade columns
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

    // The Fix: Add a check for the student's current level
    // This ignores English 7 if the student is currently Grade 11
    $grades = Grade::where('lrn', $lrn)
                   ->where('level', $student->level) // <-- ADD THIS LINE
                   ->where('is_published', true)
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
    
    // 2. UPDATED: Logic set to 8 subjects per student as requested
    $subjectsPerStudent = 8; 

    $teachers = \App\Models\TeacherIdentity::where('position', 'Teacher')
        ->get()
        ->map(function($teacher) use ($term, $subjectsPerStudent) {
            
            // 3. Convert to array to ensure the SQL 'IN' clause works on Live
            $studentIds = \App\Models\StudentIdentity::where('adviser_id', $teacher->id)->pluck('lrn')->toArray();
            $studentCount = count($studentIds);

            // Goal calculation: Total students multiplied by 8 subjects
            $totalExpectedGrades = $studentCount * $subjectsPerStudent;

            if ($studentCount > 0) {
                // Count grades already sent to admin for this specific term
                $sentGrades = \App\Models\Grade::whereIn('lrn', $studentIds)
                    ->whereRaw('LOWER(semester) = ?', [$term])
                    ->whereRaw('is_submitted_to_admin::text = ?', ['true'])
                    ->count();

                // Count grades saved as drafts but not yet sent
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

            // 4. ADDED: Logic to determine final completion status
            // Only marked completed if actual sent grades reach the 8-subject goal
            $teacher->is_completed = ($totalExpectedGrades > 0 && $sentGrades >= $totalExpectedGrades);

            return $teacher;
        });

    return view('admin.monitoring', compact('teachers', 'term'));
}
}

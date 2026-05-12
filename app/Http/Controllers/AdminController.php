<?php

namespace App\Http\Controllers;

use App\Models\TeacherIdentity;
use App\Models\StudentIdentity;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User; // Added missing import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Helper to get signatories from the database
     */
    private function getSignatories()
    {
        // Fixed: Properly closed the comment and handled null safety
        return (object)[
            'registrar' => DB::table('settings')->where('key', 'registrar')->value('value') ?? 'not set',
            'school_head' => DB::table('settings')->where('key', 'school_head')->value('value') ?? 'not set'
        ];
    }

    public function dashboard()
    {
        $totalTeachers = TeacherIdentity::count();
        $totalStudents = StudentIdentity::count();
        
        $juniorCount = StudentIdentity::whereIn('level', ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'])->count();
        $seniorCount = StudentIdentity::whereIn('level', ['Grade 11', 'Grade 12'])->count();
        
        // Fixed: Corrected 'LRN' to 'lrn' for consistency
        $pendingGrades = Grade::whereRaw('is_submitted_to_admin = CAST(? AS BOOLEAN)', [true])
            ->whereRaw('is_published = CAST(? AS BOOLEAN)', [false])
            ->distinct('lrn')
            ->count('lrn');

        $subjects = Subject::all();
        $signatories = $this->getSignatories();

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

    public function storeSubject(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'level' => 'required', 
        ]);

        Subject::create([
            'code' => $request->code,
            'name' => $request->name,
            'level' => $request->level,
        ]);

        return back()->with('success', 'Subject added successfully!');
    }

    public function deleteSubject($id)
    {
        Subject::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Subject removed successfully!');
    }

    public function showSubjects()
    {
        $subjects = Subject::all(); 
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

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:teacher_identities',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'position'   => 'required|string',
        ]);
    
        $fullname = trim($request->first_name . ' ' . ($request->middle_name ?? '') . ' ' . $request->last_name);
    
        TeacherIdentity::create([
            'employee_id' => $request->employee_id,
            'fullname'    => $fullname,
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'position'    => $request->position,
            'is_active'   => DB::raw('false'),
        ]);
    
        return redirect()->back()->with('success', 'Teacher identity authorized successfully!');
    }

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
            'is_active'   => $request->is_active ? DB::raw('true') : DB::raw('false'),
        ]);
        return redirect()->back()->with('success', 'Teacher record updated successfully!');
    }

    public function deleteTeacher($id)
    {
        $teacher = TeacherIdentity::findOrFail($id);
        User::where('identifier', $teacher->employee_id)->delete();
        $teacher->delete();
        return redirect()->back()->with('success', 'Teacher and login account moved to archive.');
    }

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

    public function deleteStudent($id)
    {
        $student = StudentIdentity::findOrFail($id);
        User::where('identifier', $student->lrn)->delete();
        $student->delete();
        return redirect()->back()->with('success', 'Student and login account moved to archive.');
    }

    public function archive()
    {
        $archivedStudents = StudentIdentity::onlyTrashed()->get();
        $archivedTeachers = TeacherIdentity::onlyTrashed()->get();
        return view('admin.archive', compact('archivedStudents', 'archivedTeachers'));
    }

    public function restoreStudent($id)
    {
        $student = StudentIdentity::withTrashed()->findOrFail($id);
        $student->restore();
        $student->update(['is_active' => true]);
        $user = User::where('identifier', $student->lrn)->first();
        if ($user) { $user->update(['is_active' => true]); }
        return redirect()->back()->with('success', 'Student restored!');
    }

    public function restoreTeacher($id)
    {
        $teacher = TeacherIdentity::withTrashed()->findOrFail($id);
        $teacher->restore();
        $teacher->update(['is_active' => true]);
        $user = User::where('identifier', $teacher->employee_id)->first();
        if ($user) { $user->update(['is_active' => true]); }
        return redirect()->back()->with('success', 'Teacher restored!');
    }

    public function forceDeleteTeacher($id)
    {
        $teacher = TeacherIdentity::withTrashed()->findOrFail($id);
        User::where('identifier', $teacher->employee_id)->forceDelete();
        $teacher->forceDelete();
        return redirect()->back()->with('success', 'Teacher permanently removed.');
    }

    public function forceDeleteAdmin($id)
    {
        $admin = \App\Models\AdminIdentity::withTrashed()->findOrFail($id);
        User::where('identifier', $admin->id_number)->forceDelete();
        $admin->forceDelete();
        return redirect()->back()->with('success', 'Admin permanently removed.');
    }

    public function forceDeleteStudent($id)
    {
        $student = StudentIdentity::withTrashed()->findOrFail($id);
        User::where('identifier', $student->lrn)->forceDelete();
        $student->forceDelete();
        return redirect()->back()->with('success', 'Student record deleted permanently.');
    }

    public function incomingGrades()
    {
        $incomingGrades = Grade::join('student_identities', 'grades.lrn', '=', 'student_identities.lrn')
            ->whereRaw('grades.is_submitted_to_admin::text = ?', ['true'])
            ->whereRaw('grades.is_published::text = ?', ['false'])
            ->whereColumn('grades.level', 'student_identities.level')
            ->select('grades.*')
            ->get()
            ->groupBy('lrn');

        return view('admin.incoming_grades', compact('incomingGrades'));
    }

    public function forwardToStudent($lrn)
    {
        Grade::where('lrn', $lrn)
            ->where('is_submitted_to_admin', DB::raw('true'))
            ->where('is_published', DB::raw('false'))
            ->update(['is_published' => DB::raw('true')]);

        return redirect()->back()->with('success', 'Grades published!');
    }

    public function generateReport($lrn)
    {
        $student = StudentIdentity::where('lrn', $lrn)->firstOrFail();
        $grades = Grade::where('lrn', $lrn)
                       ->where('level', $student->level)
                       ->where('is_published', true)
                       ->get();
        $signatories = $this->getSignatories();
        return view('admin.reports.student_grade_pdf', compact('student', 'grades', 'signatories'));
    }

    public function updateSignatories(Request $request) 
    {
        $request->validate([
            'registrar_name' => 'required|string',
            'head_name' => 'required|string',
        ]);

        DB::table('settings')->updateOrInsert(['key' => 'registrar'], ['value' => $request->registrar_name, 'updated_at' => now()]);
        DB::table('settings')->updateOrInsert(['key' => 'school_head'], ['value' => $request->head_name, 'updated_at' => now()]);

        return back()->with('success', 'Signatories updated successfully!');
    }
    
    public function monitoring(Request $request)
    {
        $term = strtolower($request->get('term', '1st term'));
        $subjectsPerStudent = 8; 

        $teachers = TeacherIdentity::where('position', 'Teacher')
            ->get()
            ->map(function($teacher) use ($term, $subjectsPerStudent) {
                $studentIds = StudentIdentity::where('adviser_id', $teacher->id)->pluck('lrn')->toArray();
                $studentCount = count($studentIds);
                $totalExpectedGrades = $studentCount * $subjectsPerStudent;

                if ($studentCount > 0) {
                    $sentGrades = Grade::whereIn('lrn', $studentIds)
                        ->whereRaw('LOWER(semester) = ?', [$term])
                        ->whereRaw('is_submitted_to_admin::text = ?', ['true'])
                        ->count();

                    $savedGrades = Grade::whereIn('lrn', $studentIds)
                        ->whereRaw('LOWER(semester) = ?', [$term])
                        ->whereRaw('is_submitted_to_admin::text = ?', ['false'])
                        ->count();
                } else {
                    $sentGrades = $savedGrades = 0;
                }

                $teacher->expected_total = $totalExpectedGrades;
                $teacher->actual_sent = $sentGrades;
                $teacher->has_drafts = ($savedGrades > 0);
                $teacher->is_completed = ($totalExpectedGrades > 0 && $sentGrades >= $totalExpectedGrades);

                return $teacher;
            });

        return view('admin.monitoring', compact('teachers', 'term'));
    }
}

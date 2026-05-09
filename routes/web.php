<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// --- PUBLIC ROUTES ---

// 1. The Home Page
Route::get('/', function () {
    return view('home');
})->name('home');

// 2. The Login Page
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login/submit', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Lost your password?
Route::get('/forgot-password', [AuthController::class, 'showResetForm'])->name('password.request');
Route::post('/forgot-password/verify', [AuthController::class, 'handleReset'])->name('password.reset.submit');

// 4. Verification & Registration
Route::get('/create-account', function(Request $request) {
    // We fetch the name so the Setup Page can show "Teacher: [Name]"
    $name = "User";
    if ($request->type === 'teacher') {
        $record = \App\Models\TeacherIdentity::find($request->id);
        $name = $record ? $record->first_name . " " . $record->last_name : "Teacher";
    } else {
        $record = \App\Models\StudentIdentity::find($request->id);
        $name = $record ? $record->fullname : "Student";
    }

    // Fixed to match your folder: auth/register_account.blade.php
    return view('auth.register_account', [
        'id' => $request->identifier, // Employee ID or LRN
        'role' => $request->type,
        'name' => $name
    ]);
})->name('register.step2');

Route::get('/verify-credentials', function() {
    return view('auth.verify');
})->name('verify.page');

Route::match(['get', 'post'], '/verify-identity', [AuthController::class, 'verifyIdentity'])->name('verify.identity');
Route::match(['get', 'post'], '/finalize-registration', [AuthController::class, 'registerAccount'])->name('register.account');


// --- ADMIN ROUTES (Protected) ---
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // --- ADDED THIS ROUTE TO FIX THE 405 ERROR ---
    Route::post('/admin/add-subject', [AdminController::class, 'storeSubject'])->name('admin.storeSubject');

    Route::get('/admin/teachers', [AdminController::class, 'teacherList'])->name('admin.teachers');
    Route::post('/admin/add-teacher', [AdminController::class, 'storeTeacher'])->name('admin.storeTeacher');
    Route::put('/admin/teachers/update/{id}', [AdminController::class, 'updateTeacher'])->name('admin.updateTeacher');
    Route::delete('/admin/teachers/delete/{id}', [AdminController::class, 'deleteTeacher'])->name('admin.deleteTeacher');
    Route::delete('/admin/teachers/force-delete/{id}', [AdminController::class, 'forceDeleteTeacher'])->name('admin.forceDeleteTeacher');
    Route::get('/admin/students/{level}', [AdminController::class, 'studentMasterlist'])->name('admin.studentList');
    Route::delete('/admin/students/delete/{id}', [AdminController::class, 'deleteStudent'])->name('admin.deleteStudent');
    Route::get('/admin/grades/incoming', [AdminController::class, 'incomingGrades'])->name('admin.incomingGrades');
    Route::post('/admin/grades/forward/{lrn}', [AdminController::class, 'forwardToStudent'])->name('admin.forwardGrades');
    Route::get('/admin/archive', [AdminController::class, 'archive'])->name('admin.archive');
    Route::post('/admin/restore-student/{id}', [AdminController::class, 'restoreStudent'])->name('admin.restoreStudent');
    Route::post('/admin/restore-teacher/{id}', [AdminController::class, 'restoreTeacher'])->name('admin.restoreTeacher');
    Route::get('/admin/report/print/{lrn}', [AdminController::class, 'generateReport'])->name('admin.printReport');
    Route::delete('/admin/students/force-delete/{id}', [AdminController::class, 'forceDeleteStudent'])->name('admin.forceDeleteStudent');
    Route::post('/admin/update-signatories', [AdminController::class, 'updateSignatories'])->name('admin.updateSignatories');
    Route::get('/admin/monitoring', [AdminController::class, 'monitoring'])->name('admin.monitoring');
    // 1. The route to VIEW the page
Route::get('/admin/subjects', [AdminController::class, 'showSubjects'])->name('admin.subjects');

// 2. The route to SAVE (This is the one missing or mismatched)
Route::post('/admin/subjects/store', [AdminController::class, 'storeSubject'])->name('admin.subjects.store');

// 3. The route to DELETE
Route::delete('/admin/subjects/{id}', [AdminController::class, 'deleteSubject'])->name('admin.deleteSubject');
});

// --- TEACHER ROUTES (Protected) ---
Route::middleware(['role:teacher'])->group(function () {
    Route::get('/teacher/students/{adviser_id}/{level?}', [TeacherController::class, 'studentList'])->name('teacher.students');
    Route::post('/teacher/add-student', [TeacherController::class, 'storeStudent'])->name('teacher.storeStudent');
    Route::post('/teacher/grade/submit', [TeacherController::class, 'submitGrade'])->name('teacher.submitGrade');
    Route::post('/teacher/grade/send-admin/{lrn}', [TeacherController::class, 'sendToAdmin'])->name('teacher.sendToAdmin');
});

// --- STUDENT ROUTES (Protected) ---
Route::middleware(['role:student'])->group(function () {
    Route::get('/student/dashboard/{lrn}', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/print-grades/{lrn}', [StudentController::class, 'printGrades'])->name('student.print');
    Route::post('/student/update-picture/{lrn}', [StudentController::class, 'updateProfilePicture'])->name('student.updatePicture');
});

// --- TEACHER ROUTES (Protected) ---
Route::middleware(['role:teacher'])->group(function () {
    Route::get('/teacher/students/{adviser_id}/{level?}', [TeacherController::class, 'studentList'])->name('teacher.students');
    
    // 1. The actual POST route for the form
    Route::post('/teacher/add-student', [TeacherController::class, 'storeStudent'])->name('teacher.storeStudent');

    // 2. THE FIX: Add this GET route. 
    // If someone accidentally "gets" this URL (like after a failed validation), 
    // it sends them back to the dashboard instead of showing an error page.
    Route::get('/teacher/add-student', function() {
        return redirect()->route('login'); // or redirect to a general dashboard
    });

    Route::post('/teacher/grade/submit', [TeacherController::class, 'submitGrade'])->name('teacher.submitGrade');
    Route::post('/teacher/grade/send-admin/{lrn}', [TeacherController::class, 'sendToAdmin'])->name('teacher.sendToAdmin');

    Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/students/{adviser_id}', [TeacherController::class, 'studentList'])->name('teacher.students');
    // ... other routes ...
    
    // Add the new route here
   Route::post('/teacher/update-student', [TeacherController::class, 'updateStudent'])->name('teacher.updateStudent');
});
});
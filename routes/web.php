<?php

use App\Http\Controllers\AdmissionPaymentsController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\ClassHallsController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\SettingsCodeController;
use App\Http\Controllers\StudentAttendancesController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\UserTypesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Welcome Page Route
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::get('/student_regiter', function () {
    return view('student_register');
})->name('student_register');

// Authentication routes - guest users සඳහා පමණි
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (login වී ඇති users සඳහා පමණි)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // System Users - Web Routes
    Route::prefix('system-users')->group(function () {
        // Page routes
        Route::get('/', [SystemUserController::class, 'viewPage'])->name('system-users.index');
        Route::get('/create', [SystemUserController::class, 'createPage'])->name('system-users.create');
        Route::get('/{id}/view', [SystemUserController::class, 'showPage'])->name('system-users.showPage');
        Route::get('/{id}/edit', [SystemUserController::class, 'editPage'])->name('system-users.edit');
        // AJAX fetch from web
        Route::get('/list', [SystemUserController::class, 'getSystemUsers'])->name('system-users.list');
    });

    // routes/web.php
    Route::prefix('user-types')->group(function () {
        Route::get('/', [UserTypesController::class, 'index'])->name('user-types.index');
        Route::get('/create', [UserTypesController::class, 'createPage'])->name('user-types.create');
        Route::get('/{id}/view', [UserTypesController::class, 'showPage'])->name('user-types.show');

        // AJAX routes for web
        Route::get('/list', [UserTypesController::class, 'getUserTypes'])->name('user-types.list');
    });

    Route::prefix('class-attendances')->group(function () {
        Route::get('/{classCategoryHasStudentClassId}', [ClassAttendanceController::class, 'indexPage'])->name('class-attendance.index');
        Route::get('/create/{classCategoryHasStudentClassId}', [ClassAttendanceController::class, 'createPage'])->name('class-attendance.create');
    });

    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('students.index');
        Route::get('/create', [StudentController::class, 'create'])->name('students.create');

        // PUT THIS ABOVE THE {id} ROUTE
        Route::get('/studentImages', [StudentController::class, 'studentImages'])->name('students.studentImages');
        Route::get('/images', [StudentController::class, 'allImages'])->name('students.images');
        Route::get('/ganarateStudentId', [StudentController::class, 'ganarateStudentId'])->name('students.ganarateStudentId');

        // ✅ FIX: Remove the duplicate 'students' from the URL
        Route::get('/add_student_to_class/{class_id}', [StudentController::class, 'addStudentToClass'])->name('students.add_student_to_class');
        Route::get('/add_student_to_single_class/{student_id}', [StudentController::class, 'addStudentToSingleClass'])->name('students.add_student_to_single_class');
        Route::get('/student_analytic/{student_id}', [StudentController::class, 'studentAnalytic'])->name('students.student_analytic');


        Route::get('/idcard/{custom_id}', [StudentController::class, 'previewCard'])->name('idcard.design1');
        Route::get('/{id}/edit', [StudentController::class, 'editPage'])->name('students.edit');
        Route::get('/{custom_id}', [StudentController::class, 'show'])->name('students.show');
    });

    Route::prefix('class-rooms')->group(function () {
        Route::get('/', [ClassRoomController::class, 'index'])->name('class_rooms.index');
        Route::get('/create', [ClassRoomController::class, 'create'])->name('class_rooms.create');
        Route::get('/schedule', [ClassRoomController::class, 'schedule'])->name('class_rooms.schedule');   // <-- FIX
        Route::get('/add_class_category/{id}', [ClassRoomController::class, 'classCategoryAdd'])->name('class_rooms.add_class_category');
        Route::get('/{id}/edit', [ClassRoomController::class, 'edit'])->name('class_rooms.edit');
        Route::get('/{id}', [ClassRoomController::class, 'show'])->name('class_rooms.show');
    });

    Route::prefix('halls')->group(function () {
        Route::get('/', [ClassHallsController::class, 'indexPage'])->name('class_halls.index');
    });



    Route::prefix('teachers')->group(function () {

        Route::get('/', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::get('/classes/{id}', [TeacherController::class, 'classes'])->name('teachers.classes');

        // 👇 Specific route (should be ABOVE /{id})
        Route::get('/view_student/{id}', [TeacherController::class, 'viewStudents'])
            ->name('teachers.view_student');

        // Dynamic routes MUST stay at the bottom
        Route::get('/{id}/edit', [TeacherController::class, 'editPage'])->name('teachers.edit');
        Route::get('/{id}', [TeacherController::class, 'show'])->name('teachers.show');
    });

    Route::prefix('admissions')->group(function () {
        Route::get('/', [AdmissionsController::class, 'indexPage'])->name('admissions.index');
    });

    Route::prefix('pay-admissions')->group(function () {
        Route::get('/', [AdmissionPaymentsController::class, 'payAdmissionPage'])
            ->name('pay-admissions.admission_payment');
    });

    Route::prefix('student-payment')->name('student-payment.')->group(function () {
        Route::get('/', [PaymentsController::class, 'indexPage'])
            ->name('index');
        Route::get('/create', [PaymentsController::class, 'createPage'])
            ->name('create');
        Route::get('/details/{student_id}/{student_class_id}', [PaymentsController::class, 'detailsPage'])
            ->name('details');
    });
    Route::prefix('student_attendance')->name('student_attendance.')->group(function () {
        Route::get('/', [StudentAttendancesController::class, 'indexPage'])
            ->name('index');  
        Route::get('/daily', [StudentAttendancesController::class, 'dailyMarkPage'])->name('daily');
    });



    // Other Pages
    Route::get('/classes', [DashboardController::class, 'classes'])->name('classes');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsCodeController::class, 'indexPage'])->name('index');
    });
});

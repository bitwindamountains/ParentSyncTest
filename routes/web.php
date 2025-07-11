<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Teacher\TeacherController as TeacherTeacherController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ConsentFormController;
use App\Http\Controllers\Teacher\EventController;
use App\Http\Controllers\Teacher\AnnouncementController;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
    Route::post('teachers/upload-csv', [App\Http\Controllers\Admin\TeacherController::class, 'uploadCsv'])->name('teachers.uploadCsv');
    Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
    Route::post('students/upload-csv', [App\Http\Controllers\Admin\StudentController::class, 'uploadCsv'])->name('students.uploadCsv');
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::resource('sections', SectionController::class)->names('sections');
    
    Route::get('/grades/{grade}/sections', [SectionController::class, 'index'])->name('grades.sections.index');
    Route::post('/grades/{grade}/sections', [SectionController::class, 'store'])->name('grades.sections.store');
    Route::post('/grades/{grade}/sections/upload-csv', [SectionController::class, 'uploadGradeCsv'])->name('grades.sections.uploadCsv');
    Route::post('/sections/{section}/upload-csv', [SectionController::class, 'uploadSectionCsv'])->name('sections.uploadCsv');
    Route::post('/sections/{section}/assign-students', [SectionController::class, 'assignStudents'])->name('sections.assignStudents');
    Route::post('/sections/{section}/assign-adviser', [SectionController::class, 'assignAdviser'])->name('sections.assignAdviser');
});

Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
 
    Route::get('/dashboard', [TeacherTeacherController::class, 'dashboard'])->name('dashboard');

    Route::get('/classes', [TeacherTeacherController::class, 'myClasses'])->name('classes.index');
    Route::get('/classes/{classId}', [TeacherTeacherController::class, 'classDetails'])->name('classes.show');
    Route::get('/sections/{sectionId}', [TeacherTeacherController::class, 'sectionDetails'])->name('sections.show');
    Route::get('/students', [TeacherTeacherController::class, 'myStudents'])->name('students.index');
    
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');
    Route::get('/attendance/sections/{gradeId}', [AttendanceController::class, 'getSections'])->name('attendance.sections');
    Route::get('/attendance/export', [AttendanceController::class, 'exportAttendance'])->name('attendance.export');

    Route::resource('announcements', AnnouncementController::class);
    Route::get('/announcements/students-by-scope', [AnnouncementController::class, 'getStudentsByScope'])->name('announcements.students-by-scope');
    Route::get('/announcements/{id}/duplicate', [AnnouncementController::class, 'duplicate'])->name('announcements.duplicate');
    Route::get('/announcements/{id}/export', [AnnouncementController::class, 'export'])->name('announcements.export');
    
    Route::resource('events', EventController::class);
    Route::get('/events/{id}/participants', [EventController::class, 'participants'])->name('events.participants');
    Route::post('/events/{id}/participants', [EventController::class, 'updateParticipants'])->name('events.update-participants');
    Route::get('/events/{id}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');
    
    Route::resource('consent-forms', ConsentFormController::class);
    Route::get('/consent-forms/{id}/signatures', [ConsentFormController::class, 'signatures'])->name('consent-forms.signatures');
    Route::get('/consent-forms/{id}/export', [ConsentFormController::class, 'exportSignatures'])->name('consent-forms.export');
    Route::get('/consent-forms/{id}/duplicate', [ConsentFormController::class, 'duplicate'])->name('consent-forms.duplicate');
});


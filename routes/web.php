<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\EventController;
use App\Http\Controllers\Teacher\ConsentFormController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Teacher Routes
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    
    // Classes and Students
    Route::get('/classes', [TeacherController::class, 'myClasses'])->name('classes.index');
    Route::get('/classes/{classId}', [TeacherController::class, 'classDetails'])->name('classes.show');
    Route::get('/sections/{sectionId}', [TeacherController::class, 'sectionDetails'])->name('sections.show');
    Route::get('/students', [TeacherController::class, 'myStudents'])->name('students.index');
    
    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');
    Route::get('/attendance/sections/{gradeId}', [AttendanceController::class, 'getSections'])->name('attendance.sections');
    Route::get('/attendance/export', [AttendanceController::class, 'exportAttendance'])->name('attendance.export');
    
    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    Route::get('/announcements/students-by-scope', [AnnouncementController::class, 'getStudentsByScope'])->name('announcements.students-by-scope');
    Route::get('/announcements/{id}/duplicate', [AnnouncementController::class, 'duplicate'])->name('announcements.duplicate');
    Route::get('/announcements/{id}/export', [AnnouncementController::class, 'export'])->name('announcements.export');
    
    // Events
    Route::resource('events', EventController::class);
    Route::get('/events/{id}/participants', [EventController::class, 'participants'])->name('events.participants');
    Route::post('/events/{id}/participants', [EventController::class, 'updateParticipants'])->name('events.update-participants');
    Route::get('/events/{id}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');
    
    // Consent Forms
    Route::resource('consent-forms', ConsentFormController::class);
    Route::get('/consent-forms/{id}/signatures', [ConsentFormController::class, 'signatures'])->name('consent-forms.signatures');
    Route::get('/consent-forms/{id}/export', [ConsentFormController::class, 'exportSignatures'])->name('consent-forms.export');
    Route::get('/consent-forms/{id}/duplicate', [ConsentFormController::class, 'duplicate'])->name('consent-forms.duplicate');
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\AuthController as APIAuthController;
use App\Http\Controllers\API\ParentController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\AttendanceController;

// PUBLIC ROUTES (No authentication required)
Route::post('/register', [APIAuthController::class, 'register']);
Route::post('/login', [APIAuthController::class, 'login']);

// PARENT-ONLY ROUTES (Following your groupmate's security design)
Route::prefix('parent')->group(function () {
    Route::post('/login', [AuthController::class, 'parentLogin']);
    
    // All parent data routes are protected by ParentMiddleware
    Route::middleware(['auth:sanctum', 'parent'])->group(function () {
        Route::post('/logout', [AuthController::class, 'parentLogout']);
        
        // Parent can only access their own data
        Route::get('/{parentId}/children', [ParentController::class, 'getChildren']);
        Route::get('/{parentId}/profile', [ParentController::class, 'getProfile']);
        Route::put('/{parentId}/profile', [ParentController::class, 'updateProfile']);
        Route::get('/{parentId}/announcements', [AnnouncementController::class, 'getParentAnnouncements']);
        Route::get('/{parentId}/events', [EventController::class, 'getParentEvents']);
    });
});

// STUDENT ROUTES (Protected - only parents can access their children's data)
Route::prefix('student')->middleware(['auth:sanctum', 'parent'])->group(function () {
    Route::get('/{studentId}/profile', [StudentController::class, 'getProfile']);
    Route::get('/{studentId}/attendance', [AttendanceController::class, 'getStudentAttendance']);
    Route::get('/{studentId}/announcements', [AnnouncementController::class, 'getStudentAnnouncements']);
});

// GENERAL AUTHENTICATED ROUTES
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [APIAuthController::class, 'logout']);
    
    // General announcements and events (for any authenticated user)
    Route::prefix('announcements')->group(function () {
        Route::get('/{announcementId}', [AnnouncementController::class, 'getAnnouncement']);
    });
    
    Route::prefix('events')->group(function () {
        Route::get('/{eventId}', [EventController::class, 'getEvent']);
        Route::post('/{eventId}/participate', [EventController::class, 'participateInEvent']);
    });
});
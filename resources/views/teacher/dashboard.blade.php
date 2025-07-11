@extends('layouts.teacher')

@section('title', 'Dashboard - ParentSync Teacher Portal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <span class="text-muted">
                <i class="fas fa-clock me-1"></i>
                Welcome back, {{ $teacher->full_name }}!
            </span>
        </div>
    </div>
</div>

<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Welcome to ParentSync Teacher Portal
                        </h4>
                        <p class="mb-0 opacity-75">
                            Manage your classes, track attendance, and communicate with parents efficiently.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex flex-column">
                            <small class="opacity-75">Today is</small>
                            <strong>{{ now()->format('l, F d, Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-primary fw-bold small mb-1">
                            Total Students
                        </div>
                        <div class="h3 mb-0 fw-bold text-gray-800">{{ $totalStudents }}</div>
                        <div class="text-success small">
                            <i class="fas fa-arrow-up me-1"></i>
                            Active in {{ $sections->count() }} sections
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-success fw-bold small mb-1">
                            My Sections
                        </div>
                        <div class="h3 mb-0 fw-bold text-gray-800">{{ $sections->count() }}</div>
                        <div class="text-success small">
                            <i class="fas fa-check me-1"></i>
                            All active
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-chalkboard fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-info fw-bold small mb-1">
                            My Classes
                        </div>
                        <div class="h3 mb-0 fw-bold text-gray-800">{{ $classes->count() }}</div>
                        <div class="text-info small">
                            <i class="fas fa-book me-1"></i>
                            {{ $classes->unique('subject_id')->count() }} subjects
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-book fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-warning fw-bold small mb-1">
                            Pending Forms
                        </div>
                        <div class="h3 mb-0 fw-bold text-gray-800">{{ $pendingConsentForms->count() }}</div>
                        <div class="text-warning small">
                            <i class="fas fa-clock me-1"></i>
                            Awaiting signatures
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-file-signature fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Recent Announcements -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-bullhorn me-2"></i>
                    Recent Announcements
                </h6>
                <a href="{{ route('teacher.announcements.index') }}" class="btn btn-sm btn-light">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($recentAnnouncements->count() > 0)
                    @foreach($recentAnnouncements as $announcement)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1 fw-bold">{{ $announcement->title }}</h6>
                                <span class="badge bg-secondary">{{ $announcement->scope_display }}</span>
                            </div>
                            <p class="text-muted small mb-2">{{ Str::limit($announcement->content, 120) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $announcement->created_at ? $announcement->created_at->diffForHumans() : 'No date' }}

                                </small>
                                <a href="{{ route('teacher.announcements.show', $announcement->announcement_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent announcements.</p>
                        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create Announcement
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Upcoming Events
                </h6>
                <a href="{{ route('teacher.events.index') }}" class="btn btn-sm btn-light">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($upcomingEvents->count() > 0)
                    @foreach($upcomingEvents as $event)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1 fw-bold">{{ $event->title }}</h6>
                                <span class="badge bg-info">{{ $event->date->format('M d, Y') }}</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $event->location }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $event->time }}
                                </small>
                                <a href="{{ route('teacher.events.show', $event->event_id) }}" 
                                   class="btn btn-sm btn-outline-success">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No upcoming events.</p>
                        <a href="{{ route('teacher.events.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Create Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sections and Classes Row -->
<div class="row">
    <!-- My Sections -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-layer-group me-2"></i>
                    My Sections
                </h6>
            </div>
            <div class="card-body">
                @if($sections->count() > 0)
                    @foreach($sections as $section)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1 fw-bold">{{ $section->section_name }}</h6>
                                <span class="badge bg-success">{{ $section->students->count() }} students</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Grade {{ $section->grade->grade_level ?? 'N/A' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>
                                    {{ $section->classes->count() }} classes
                                </small>
                                <a href="{{ route('teacher.sections.show', $section->section_id) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No sections assigned.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Classes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    My Classes
                </h6>
            </div>
            <div class="card-body">
                @if($classes->count() > 0)
                    @foreach($classes as $class)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1 fw-bold">{{ $class->display_name }}</h6>
                                <span class="badge bg-info">{{ $class->school_year ?? 'N/A' }}</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-layer-group me-1"></i>
                                {{ $class->section->section_name ?? 'N/A' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $class->section->students->count() }} students
                                </small>
                                <a href="{{ route('teacher.classes.show', $class->class_id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No classes assigned.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-tools me-2"></i>
                    Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                            <span>Mark Attendance</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="fas fa-bullhorn fa-2x mb-2"></i>
                            <span>Create Announcement</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.create') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                            <span>Create Event</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.create') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                            <i class="fas fa-file-signature fa-2x mb-2"></i>
                            <span>Create Consent Form</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2"></i>
                    Recent Activity
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-clipboard-check fa-2x text-success mb-2"></i>
                            <h5 class="mb-1">{{ $totalStudents }}</h5>
                            <small class="text-muted">Students Today</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-bullhorn fa-2x text-primary mb-2"></i>
                            <h5 class="mb-1">{{ $recentAnnouncements->count() }}</h5>
                            <small class="text-muted">Recent Announcements</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                            <h5 class="mb-1">{{ $upcomingEvents->count() }}</h5>
                            <small class="text-muted">Upcoming Events</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush 
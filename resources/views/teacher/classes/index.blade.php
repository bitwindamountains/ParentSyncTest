@extends('layouts.teacher')

@section('title', 'My Classes - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Classes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $classes->count() }}</h4>
                        <small>Total Classes</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $classes->sum(function($class) { return $class->section->students->count(); }) }}</h4>
                        <small>Total Students</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $classes->unique('section_id')->count() }}</h4>
                        <small>Sections</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $classes->unique('subject_id')->count() }}</h4>
                        <small>Subjects</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classes List -->
<div class="row">
    @if($classes->count() > 0)
        @foreach($classes as $class)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $class->class_name ?? $class->subject->subject_name }}</h5>
                            <span class="badge bg-light text-dark">
                                {{ $class->section->section_name }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Subject:</strong><br>
                            <span class="text-muted">{{ $class->subject->subject_name }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Section:</strong><br>
                            <span class="text-muted">
                                {{ $class->section->section_name }} 
                                (Grade {{ $class->section->grade->grade_level }})
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Students:</strong><br>
                            <span class="text-muted">
                                <i class="fas fa-users me-1"></i>
                                {{ $class->section->students->count() }} students
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>School Year:</strong><br>
                            <span class="text-muted">{{ $class->school_year }}</span>
                        </div>
                        
                        @if($class->section->students->count() > 0)
                            <div class="mb-3">
                                <strong>Student List:</strong><br>
                                <div class="list-group list-group-flush">
                                    @foreach($class->section->students->take(3) as $student)
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                            <div>
                                                <strong>{{ $student->full_name }}</strong><br>
                                                <small class="text-muted">ID: {{ $student->student_id }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($class->section->students->count() > 3)
                                        <div class="list-group-item text-center py-2">
                                            <small class="text-muted">
                                                And {{ $class->section->students->count() - 3 }} more students...
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.classes.show', $class->class_id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                View Details
                            </a>
                            <a href="{{ route('teacher.attendance.index', ['class_id' => $class->class_id]) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-clipboard-check me-1"></i>
                                Take Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Classes Assigned</h4>
                    <p class="text-muted">You haven't been assigned to any classes yet.</p>
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.students.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-users me-2"></i>
                            View All Students
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Take Attendance
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-info w-100">
                            <i class="fas fa-bullhorn me-2"></i>
                            Create Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
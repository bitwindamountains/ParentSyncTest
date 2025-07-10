@extends('layouts.teacher')

@section('title', $class->class_name ?? $class->subject->subject_name . ' - Class Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $class->class_name ?? $class->subject->subject_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.classes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Classes
            </a>
            <a href="{{ route('teacher.attendance.index', ['class_id' => $class->class_id]) }}" class="btn btn-success">
                <i class="fas fa-clipboard-check me-1"></i>
                Take Attendance
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Class Information -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Class Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Subject:</strong><br>
                        <span class="text-muted">{{ $class->subject->subject_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Section:</strong><br>
                        <span class="text-muted">
                            {{ $class->section->section_name }} 
                            (Grade {{ $class->section->grade->grade_level }})
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>School Year:</strong><br>
                        <span class="text-muted">{{ $class->school_year }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Students:</strong><br>
                        <span class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            {{ $class->section->students->count() }} students
                        </span>
                    </div>
                </div>
                
                @if($class->section->grade->school)
                    <div class="mb-3">
                        <strong>School:</strong><br>
                        <span class="text-muted">{{ $class->section->grade->school->school_name }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Quick Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-0">{{ $class->section->students->count() }}</h4>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-0">{{ $class->section->students->where('gender', 'female')->count() }}</h4>
                            <small class="text-muted">Female</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-0">{{ $class->section->students->where('gender', 'male')->count() }}</h4>
                            <small class="text-muted">Male</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-0">{{ $class->section->students->where('grade_level', $class->section->grade->grade_level)->count() }}</h4>
                            <small class="text-muted">Grade Level</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Students in this Class
                </h5>
            </div>
            <div class="card-body">
                @if($class->section->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Birthdate</th>
                                    <th>Grade Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($class->section->students as $student)
                                    <tr>
                                        <td>
                                            <strong>{{ $student->student_id }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $student->full_name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ optional($student->birthdate)->format('M d, Y') ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $student->grade_level }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('teacher.students.show', $student->student_id) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('teacher.attendance.index', ['student_id' => $student->student_id]) }}" 
                                                   class="btn btn-outline-success">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Students in this Class</h5>
                        <p class="text-muted">This class doesn't have any students assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
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
                        <a href="{{ route('teacher.attendance.index', ['class_id' => $class->class_id]) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Take Attendance
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.create', ['class_id' => $class->class_id]) }}" 
                           class="btn btn-info w-100">
                            <i class="fas fa-bullhorn me-2"></i>
                            Create Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.create', ['class_id' => $class->class_id]) }}" 
                           class="btn btn-warning w-100">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Create Event
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.classes.index') }}" 
                           class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Classes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@extends('layouts.teacher')

@section('title', $student->full_name . ' - Student Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $student->full_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.students.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Students
            </a>
            <a href="{{ route('teacher.attendance.index', ['student_id' => $student->student_id]) }}" class="btn btn-success">
                <i class="fas fa-clipboard-check me-1"></i>
                Take Attendance
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Student Information -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-graduate me-2"></i>
                    Student Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Student ID:</strong><br>
                        <span class="text-muted">{{ $student->student_id }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Full Name:</strong><br>
                        <span class="text-muted">{{ $student->full_name }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Section:</strong><br>
                        <span class="text-muted">
                            {{ $student->section->section_name ?? 'N/A' }} 
                            (Grade {{ $student->section->grade->grade_level ?? 'N/A' }})
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Grade Level:</strong><br>
                        <span class="badge bg-info">{{ $student->grade_level }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Birthdate:</strong><br>
                        <span class="text-muted">
                            {{ optional($student->birthdate)->format('l, F d, Y') ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Age:</strong><br>
                        <span class="text-muted">
                            {{ $student->birthdate ? $student->birthdate->age . ' years old' : 'N/A' }}
                        </span>
                    </div>
                </div>
                
                @if($student->section->grade->school)
                    <div class="mb-3">
                        <strong>School:</strong><br>
                        <span class="text-muted">{{ $student->section->grade->school->school_name }}</span>
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
                            <h4 class="text-primary mb-0">{{ $student->parents->count() }}</h4>
                            <small class="text-muted">Parents</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-0">{{ $student->attendanceRecords->count() }}</h4>
                            <small class="text-muted">Attendance Records</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-0">{{ $student->announcementRecipients->count() }}</h4>
                            <small class="text-muted">Announcements</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-0">{{ $student->consentSignatures->count() }}</h4>
                            <small class="text-muted">Consent Forms</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance -->
@if($student->attendanceRecords->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Recent Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Marked By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->attendanceRecords->take(10) as $record)
                                    <tr>
                                        <td>{{ optional($record->date)->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>
                                            @if($record->status === 'present')
                                                <span class="badge bg-success">Present</span>
                                            @elseif($record->status === 'absent')
                                                <span class="badge bg-danger">Absent</span>
                                            @elseif($record->status === 'late')
                                                <span class="badge bg-warning text-dark">Late</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->teacher->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit($record->notes, 50) }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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
                        <a href="{{ route('teacher.attendance.index', ['student_id' => $student->student_id]) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Take Attendance
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.sections.show', $student->section->section_id) }}" 
                           class="btn btn-info w-100">
                            <i class="fas fa-layer-group me-2"></i>
                            View Section
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.create', ['student_id' => $student->student_id]) }}" 
                           class="btn btn-warning w-100">
                            <i class="fas fa-bullhorn me-2"></i>
                            Send Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.students.index') }}" 
                           class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
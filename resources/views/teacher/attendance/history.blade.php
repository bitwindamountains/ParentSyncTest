@extends('layouts.teacher')

@section('title', 'Attendance History - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Attendance History</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Attendance
            </a>
            <a href="{{ route('teacher.attendance.summary') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-1"></i>
                Summary
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.attendance.history') }}" class="row g-3">
            <div class="col-md-2">
                <label for="date_from" class="form-label">From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-2">
                <label for="grade_id" class="form-label">Grade</label>
                <select class="form-select" id="grade_id" name="grade_id">
                    <option value="">All Grades</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->grade_id }}" {{ $selectedGrade == $grade->grade_id ? 'selected' : '' }}>
                            Grade {{ $grade->grade_level }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="section_id" class="form-label">Section</label>
                <select class="form-select" id="section_id" name="section_id">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->section_id }}" {{ $selectedSection == $section->section_id ? 'selected' : '' }}>
                            {{ $section->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="subject_id" class="form-label">Subject</label>
                <select class="form-select" id="subject_id" name="subject_id">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->subject_id }}" {{ $selectedSubject == $subject->subject_id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-select" id="student_id" name="student_id">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->student_id }}" {{ $selectedStudent == $student->student_id ? 'selected' : '' }}>
                            {{ $student->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="present" {{ $selectedStatus == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ $selectedStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ $selectedStatus == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="excused" {{ $selectedStatus == 'excused' ? 'selected' : '' }}>Excused</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>
                    Filter
                </button>
                <a href="{{ route('teacher.attendance.history') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Records Table -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>
            Attendance Records
        </h5>
    </div>
    <div class="card-body">
        @if($attendanceRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Marked By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceRecords as $record)
                            <tr>
                                <td>
                                    <strong>{{ optional($record->date)->format('M d, Y') ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ optional($record->date)->format('l') ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $record->student->full_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">ID: {{ $record->student->student_id ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $record->section->section_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $record->subject->subject_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->status === 'present')
                                        <span class="badge bg-success">Present</span>
                                    @elseif($record->status === 'absent')
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif($record->status === 'late')
                                        <span class="badge bg-warning text-dark">Late</span>
                                    @elseif($record->status === 'excused')
                                        <span class="badge bg-info text-dark">Excused</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $record->marked_by ?? 'N/A' }}
                                    </span>
                                </td>
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
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $attendanceRecords->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No Attendance Records Found</h4>
                <p class="text-muted">No attendance records match your current filters.</p>
                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Take Attendance
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 
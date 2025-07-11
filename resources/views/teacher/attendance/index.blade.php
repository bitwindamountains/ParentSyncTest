@extends('layouts.teacher')

@section('title', 'Mark Attendance - ParentSync Teacher Portal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mark Attendance</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history me-1"></i> History
            </a>
            <a href="{{ route('teacher.attendance.summary') }}" class="btn btn-outline-success">
                <i class="fas fa-chart-bar me-1"></i> Summary
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Step 1: Select Date, Grade, Section & Subject</h5>
    </div>
    <div class="card-body">
        <form id="attendance-selection-form" method="GET" action="{{ route('teacher.attendance.index') }}" class="row g-3">
            <div class="col-md-2">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" max="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-2">
                <label for="grade_id" class="form-label">Grade</label>
                <select name="grade_id" id="grade_id" class="form-select">
                    <option value="">Select Grade</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->grade_id }}" {{ request('grade_id') == $grade->grade_id ? 'selected' : '' }}>
                            Grade {{ $grade->grade_level }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="section_id" class="form-label">Section</label>
                <select name="section_id" id="section_id" class="form-select">
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->section_id }}" {{ request('section_id') == $section->section_id ? 'selected' : '' }}>
                            {{ $section->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="search" class="form-label">Search Student</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Last name...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" id="attendance-submit-btn">
                    <i class="fas fa-search me-1"></i> Load Students
                </button>
            </div>
        </form>
    </div>
</div>

@if($students->count() > 0)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Step 2: Mark Attendance for
                @if($selectedGrade && $selectedSection)
                    Grade {{ $selectedGrade->grade_level }} - {{ $selectedSection->section_name }}
                @endif
                - {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
            </h5>
            <div>
                <button type="button" class="btn btn-outline-success btn-sm" id="mark-all-present">
                    <i class="fas fa-user-check me-1"></i> Mark All Present
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.attendance.store') }}">
                @csrf
                <input type="hidden" name="section_id" value="{{ $selectedSection->section_id ?? '' }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php $existingRecord = $attendanceRecords->get($student->student_id); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $student->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $student->student_id }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                                <input type="radio" class="btn-check attendance-status" 
                                                       name="attendance[{{ $loop->parent->index }}][status]" 
                                                       id="{{ $status }}_{{ $student->student_id }}" 
                                                       value="{{ $status }}"
                                                       {{ ($existingRecord && $existingRecord->status == $status) || (!$existingRecord && $status == 'present') ? 'checked' : '' }}>
                                                <label class="btn btn-outline-{{
                                                    $status === 'present' ? 'success' : (
                                                    $status === 'absent' ? 'danger' : (
                                                    $status === 'late' ? 'warning' : 'info')
                                                ) }} btn-sm attendance-label" 
                                                for="{{ $status }}_{{ $student->student_id }}">
                                                    {{ ucfirst($status) }}
                                                </label>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" 
                                               value="{{ $student->student_id }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="attendance[{{ $loop->index }}][notes]" 
                                               value="{{ $existingRecord->notes ?? '' }}"
                                               placeholder="Optional notes...">
                                        @if($existingRecord)
                                            <small class="text-muted">Last marked: {{ optional($existingRecord->created_at)->format('M d, Y H:i') ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <span class="text-muted">Total Students: {{ $students->count() }}</span>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
@elseif(request('grade_id') || request('section_id'))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No students found for the selected criteria. Please check your selection.
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade_id');
    const sectionSelect = document.getElementById('section_id');
    const searchInput = document.getElementById('search');
    const submitBtn = document.getElementById('attendance-submit-btn');
    const form = document.getElementById('attendance-selection-form');

    // Grade change handler
    gradeSelect.addEventListener('change', function() {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        
        if (this.value) {
            // Load sections for selected grade
            fetch(`/teacher/attendance/sections/${this.value}`)
                .then(response => response.json())
                .then(sections => {
                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.section_id;
                        option.textContent = section.section_name;
                        sectionSelect.appendChild(option);
                    });
                });
        }
    });

    // Search button click handler
    submitBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Add form data to params
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Navigate to the same page with the form data
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    });

    // Mark all present
    document.getElementById('mark-all-present')?.addEventListener('click', function() {
        document.querySelectorAll('.attendance-status[value="present"]').forEach(function(radio) {
            radio.checked = true;
        });
    });
});
</script>
@endpush
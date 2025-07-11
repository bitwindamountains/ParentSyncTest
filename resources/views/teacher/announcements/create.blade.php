@extends('layouts.teacher')

@section('title', 'Create Announcement - ParentSync Teacher Portal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Announcement</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Announcements
        </a>
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

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">New Announcement</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.announcements.store') }}" id="announcementForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                        <select class="form-select @error('scope') is-invalid @enderror" 
                                id="scope" name="scope" required>
                            <option value="">Select Scope</option>
                            <option value="section" {{ old('scope') == 'section' ? 'selected' : '' }}>Section/Grade</option>
                            <option value="class" {{ old('scope') == 'class' ? 'selected' : '' }}>Class-wide</option>
                            <option value="individual" {{ old('scope') == 'individual' ? 'selected' : '' }}>Individual Students</option>
                        </select>
                        @error('scope')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Section Selection (shown when scope is 'section') -->
                    <div class="mb-3" id="sectionSelection" style="display: none;">
                        <label for="section_id" class="form-label">Select Section</label>
                        @if($sections->count() > 0)
                        <select class="form-select @error('section_id') is-invalid @enderror" 
                                id="section_id" name="section_id">
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->section_id }}" 
                                        {{ old('section_id') == $section->section_id ? 'selected' : '' }}>
                                    {{ $section->section_name }} (Grade {{ $section->grade->grade_level ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @else
                        <div class="alert alert-warning mt-2">You have no sections assigned.</div>
                        @endif
                        @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Class Selection (shown when scope is 'class') -->
                    <div class="mb-3" id="classSelection" style="display: none;">
                        <label for="class_id" class="form-label">Select Class</label>
                        @if($classes->count() > 0)
                        <select class="form-select @error('class_id') is-invalid @enderror" 
                                id="class_id" name="class_id">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}" 
                                        {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @else
                        <div class="alert alert-warning mt-2">You have no classes assigned.</div>
                        @endif
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Individual Students Selection (shown when scope is 'individual') -->
                    <div class="mb-3" id="individualSelection" style="display: none;">
                        <label class="form-label">Select Students</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($students as $student)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="student_ids[]" value="{{ $student->student_id }}" 
                                           id="student_{{ $student->student_id }}"
                                           {{ in_array($student->student_id, old('student_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="student_{{ $student->student_id }}">
                                        {{ $student->full_name }} 
                                        <small class="text-muted">({{ $student->section->section_name ?? 'N/A' }})</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('student_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Create Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Scope Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-success">Section/Grade</h6>
                    <p class="small text-muted">Announcement will be visible to all students in the selected section and their teachers.</p>
                </div>
                <div class="mb-3">
                    <h6 class="text-info">Class-wide</h6>
                    <p class="small text-muted">Announcement will be visible to all students in the selected class and their teachers.</p>
                </div>
                <div class="mb-3">
                    <h6 class="text-warning">Individual Students</h6>
                    <p class="small text-muted">Announcement will be visible only to the selected students and their teachers.</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Preview</h6>
            </div>
            <div class="card-body">
                <div id="preview">
                    <p class="text-muted small">Start typing to see a preview of your announcement.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeSelect = document.getElementById('scope');
    const sectionSelection = document.getElementById('sectionSelection');
    const classSelection = document.getElementById('classSelection');
    const individualSelection = document.getElementById('individualSelection');
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const preview = document.getElementById('preview');

    // Show/hide scope-specific fields
    function toggleScopeFields() {
        const scope = scopeSelect.value;
        
        // Hide all scope-specific fields
        sectionSelection.style.display = 'none';
        classSelection.style.display = 'none';
        individualSelection.style.display = 'none';
        
        // Show relevant field based on scope
        switch(scope) {
            case 'section':
                sectionSelection.style.display = 'block';
                break;
            case 'class':
                classSelection.style.display = 'block';
                break;
            case 'individual':
                individualSelection.style.display = 'block';
                break;
        }
    }

    // Update preview
    function updatePreview() {
        const title = titleInput.value || 'Announcement Title';
        const content = contentInput.value || 'Announcement content will appear here...';
        
        preview.innerHTML = `
            <h6 class="text-primary">${title}</h6>
            <p class="small">${content}</p>
            <small class="text-muted">Created by: {{ Auth::user()->teacher->full_name }}</small>
        `;
    }

    // Event listeners
    scopeSelect.addEventListener('change', toggleScopeFields);
    titleInput.addEventListener('input', updatePreview);
    contentInput.addEventListener('input', updatePreview);

    // Initialize
    toggleScopeFields();
    updatePreview();

    // Prevent form submission if no valid section/class
    const form = document.getElementById('announcementForm');
    form.addEventListener('submit', function(e) {
        const scope = document.getElementById('scope').value;
        if (scope === 'section' && (!document.getElementById('section_id') || document.getElementById('section_id').options.length <= 1)) {
            e.preventDefault();
            alert('No valid section available.');
        }
        if (scope === 'class' && (!document.getElementById('class_id') || document.getElementById('class_id').options.length <= 1)) {
            e.preventDefault();
            alert('No valid class available.');
        }
    });
});
</script>
@endpush 
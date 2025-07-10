@extends('layouts.teacher')

@section('title', 'Edit Announcement - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Announcement</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.announcements.show', $announcement->announcement_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Announcement
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

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Edit Announcement
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.announcements.update', $announcement->announcement_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                                <select class="form-select @error('scope') is-invalid @enderror" id="scope" name="scope" required>
                                    <option value="">Select Scope</option>
                                    <option value="section" {{ old('scope', $announcement->scope) === 'section' ? 'selected' : '' }}>
                                        Section/Grade
                                    </option>
                                    <option value="class" {{ old('scope', $announcement->scope) === 'class' ? 'selected' : '' }}>
                                        Class-wide
                                    </option>
                                    <option value="individual" {{ old('scope', $announcement->scope) === 'individual' ? 'selected' : '' }}>
                                        Individual Students
                                    </option>
                                </select>
                                @error('scope')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1"
                                           {{ old('is_urgent', $announcement->is_urgent) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_urgent">
                                        <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                        Mark as Urgent
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Scope Selection -->
                    <div id="scope-options" class="mb-3" style="display: none;">
                        <!-- Section Selection -->
                        <div id="section-selection" class="scope-option" style="display: none;">
                            <label for="section_id" class="form-label">Select Section</label>
                            <select class="form-select @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->section_id }}" 
                                            {{ old('section_id', $announcement->section_id) == $section->section_id ? 'selected' : '' }}>
                                        {{ $section->section_name }} (Grade {{ $section->grade->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Class Selection -->
                        <div id="class-selection" class="scope-option" style="display: none;">
                            <label for="class_id" class="form-label">Select Class</label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}" 
                                            {{ old('class_id', $announcement->class_id) == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }} - {{ $class->subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Individual Students Selection -->
                        <div id="individual-selection" class="scope-option" style="display: none;">
                            <label class="form-label">Select Students</label>
                            <div class="border rounded p-3 bg-light">
                                @foreach($sections as $section)
                                    <div class="mb-3">
                                        <h6>{{ $section->section_name }} (Grade {{ $section->grade->grade_level }})</h6>
                                        @foreach($section->students as $student)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="student_ids[]" value="{{ $student->student_id }}"
                                                       id="student_{{ $student->student_id }}"
                                                       {{ in_array($student->student_id, old('student_ids', $announcement->recipients->pluck('student_id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="student_{{ $student->student_id }}">
                                                    {{ $student->full_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            @error('student_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control @error('attachment') is-invalid @enderror" 
                               id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Allowed file types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG (Max: 5MB)
                        </div>
                        
                        @if($announcement->attachment_path)
                            <div class="mt-2">
                                <strong>Current Attachment:</strong><br>
                                <a href="{{ asset('storage/' . $announcement->attachment_path) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>
                                    {{ basename($announcement->attachment_path) }}
                                </a>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_attachment" name="remove_attachment" value="1">
                                    <label class="form-check-label" for="remove_attachment">
                                        Remove current attachment
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teacher.announcements.show', $announcement->announcement_id) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Update Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Panel -->
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Live Preview
                </h5>
            </div>
            <div class="card-body">
                <div id="preview-content">
                    <div class="mb-3">
                        <strong id="preview-title">Title will appear here</strong>
                    </div>
                    <div class="mb-3">
                        <span id="preview-scope" class="badge bg-secondary">Scope</span>
                        <span id="preview-urgent" class="badge bg-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-1"></i>Urgent
                        </span>
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <div id="preview-text">Content will appear here...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Information -->
        <div class="card shadow mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Help & Tips
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Scope Guidelines:</h6>
                    <ul class="small text-muted">
                        <li><strong>General:</strong> For all parents and students</li>
                        <li><strong>Section:</strong> For specific grade sections</li>
                        <li><strong>Class:</strong> For specific subject classes</li>
                        <li><strong>Individual:</strong> For specific students only</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6>Best Practices:</h6>
                    <ul class="small text-muted">
                        <li>Keep titles clear and concise</li>
                        <li>Use urgent flag sparingly</li>
                        <li>Include relevant attachments</li>
                        <li>Proofread before sending</li>
                    </ul>
                </div>
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
                        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list me-2"></i>
                            All Announcements
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus me-2"></i>
                            New Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Events
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary w-100">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeSelect = document.getElementById('scope');
    const scopeOptions = document.getElementById('scope-options');
    const sectionSelection = document.getElementById('section-selection');
    const classSelection = document.getElementById('class-selection');
    const individualSelection = document.getElementById('individual-selection');
    
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const urgentCheckbox = document.getElementById('is_urgent');
    
    const previewTitle = document.getElementById('preview-title');
    const previewText = document.getElementById('preview-text');
    const previewScope = document.getElementById('preview-scope');
    const previewUrgent = document.getElementById('preview-urgent');
    
    // Scope change handler
    scopeSelect.addEventListener('change', function() {
        const scope = this.value;
        
        // Hide all scope options
        document.querySelectorAll('.scope-option').forEach(option => {
            option.style.display = 'none';
        });
        
        // Show relevant scope option
        if (scope === 'section') {
            sectionSelection.style.display = 'block';
        } else if (scope === 'class') {
            classSelection.style.display = 'block';
        } else if (scope === 'individual') {
            individualSelection.style.display = 'block';
        }
        
        // Show/hide scope options container
        scopeOptions.style.display = (scope === 'general') ? 'none' : 'block';
        
        // Update preview
        updatePreview();
    });
    
    // Live preview updates
    titleInput.addEventListener('input', updatePreview);
    contentInput.addEventListener('input', updatePreview);
    urgentCheckbox.addEventListener('change', updatePreview);
    scopeSelect.addEventListener('change', updatePreview);
    
    function updatePreview() {
        previewTitle.textContent = titleInput.value || 'Title will appear here';
        previewText.textContent = contentInput.value || 'Content will appear here...';
        
        const scope = scopeSelect.value;
        const scopeText = scopeSelect.options[scopeSelect.selectedIndex]?.text || 'Scope';
        previewScope.textContent = scopeText.split(' ')[0];
        
        previewUrgent.style.display = urgentCheckbox.checked ? 'inline-block' : 'none';
    }
    
    // Initialize preview
    updatePreview();
    
    // Trigger scope change on load to show correct options
    scopeSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush 
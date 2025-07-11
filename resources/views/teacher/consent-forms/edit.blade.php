@extends('layouts.teacher')

@section('title', 'Edit Consent Form - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Consent Form</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.consent-forms.show', $consentForm->form_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Consent Form
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
                    Edit Consent Form
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.consent-forms.update', $consentForm->form_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Form Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $consentForm->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="6" required>{{ old('description', $consentForm->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Provide a clear description of what this consent form is for.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" name="deadline" 
                                       value="{{ old('deadline', optional($consentForm->deadline)->format('Y-m-d\TH:i') ?? '') }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                                <select class="form-select @error('scope') is-invalid @enderror" id="scope" name="scope" required>
                                    <option value="">Select Scope</option>
                                    <option value="section" {{ old('scope', $consentForm->scope) === 'section' ? 'selected' : '' }}>
                                        Section/Grade
                                    </option>
                                    <option value="class" {{ old('scope', $consentForm->scope) === 'class' ? 'selected' : '' }}>
                                        Class-wide
                                    </option>
                                </select>
                                @error('scope')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                            {{ old('section_id', $consentForm->section_id) == $section->section_id ? 'selected' : '' }}>
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
                                            {{ old('class_id', $consentForm->class_id) == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }} - {{ $class->subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="event_id" class="form-label">Related Event (Optional)</label>
                        <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id">
                            <option value="">Select Event (Optional)</option>
                            @foreach($events as $event)
                                <option value="{{ $event->event_id }}" 
                                        {{ old('event_id', $consentForm->event_id) == $event->event_id ? 'selected' : '' }}>
                                    {{ $event->title }} ({{ optional($event->date)->format('M d, Y') ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Link this consent form to a specific event if applicable.</div>
                    </div>

                    <div class="mb-3">
                        <label for="recipient_type" class="form-label">Recipient Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('recipient_type') is-invalid @enderror" id="recipient_type" name="recipient_type" required>
                            <option value="">Select Recipient Type</option>
                            <option value="section" {{ old('recipient_type', $consentForm->section_id ? 'section' : ($consentForm->class_id ? 'class' : (count($consentForm->recipients) > 0 ? 'students' : ''))) === 'section' ? 'selected' : '' }}>All students in section</option>
                            <option value="class" {{ old('recipient_type', $consentForm->class_id ? 'class' : '') === 'class' ? 'selected' : '' }}>All students in class</option>
                            <option value="students" {{ old('recipient_type', (count($consentForm->recipients) > 0 ? 'students' : '')) === 'students' ? 'selected' : '' }}>Specific student(s)</option>
                        </select>
                        @error('recipient_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dynamic Recipient Selection -->
                    <div id="recipient-options" class="mb-3" style="display: none;">
                        <!-- Section Selection -->
                        <div id="section-selection" class="scope-option" style="display: none;">
                            <label for="section_id" class="form-label">Select Section</label>
                            <select class="form-select @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->section_id }}" {{ old('section_id', $consentForm->section_id) == $section->section_id ? 'selected' : '' }}>
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
                                    <option value="{{ $class->class_id }}" {{ old('class_id', $consentForm->class_id) == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }} - {{ $class->subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student Multi-Select -->
                        <div id="students-selection" class="scope-option" style="display: none;">
                            <label for="student_ids" class="form-label">Select Student(s)</label>
                            <select class="form-select @error('student_ids') is-invalid @enderror" id="student_ids" name="student_ids[]" multiple>
                                @foreach($students as $student)
                                    <option value="{{ $student->student_id }}" {{ (collect(old('student_ids', $consentForm->recipients->pluck('student_id')->toArray()))->contains($student->student_id)) ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }} (ID: {{ $student->student_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can select one or more students. Start typing to search.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="form_content" class="form-label">Form Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('form_content') is-invalid @enderror" 
                                  id="form_content" name="form_content" rows="10" required>{{ old('form_content', $consentForm->form_content) }}</textarea>
                        @error('form_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Enter the content of the consent form. You can use HTML tags for formatting.
                            <br>Example: &lt;p&gt;I consent to...&lt;/p&gt;
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
                        
                        @if($consentForm->attachment_path)
                            <div class="mt-2">
                                <strong>Current Attachment:</strong><br>
                                <a href="{{ asset('storage/' . $consentForm->attachment_path) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>
                                    {{ basename($consentForm->attachment_path) }}
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
                        <a href="{{ route('teacher.consent-forms.show', $consentForm->form_id) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Update Consent Form
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
                        <strong id="preview-title">Form Title</strong>
                    </div>
                    <div class="mb-3">
                        <span id="preview-scope" class="badge bg-secondary">Scope</span>
                    </div>
                    <div class="mb-3">
                        <strong>Deadline:</strong><br>
                        <span id="preview-deadline" class="text-muted">Deadline will appear here</span>
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <div id="preview-description">Form description will appear here...</div>
                    </div>
                    <div class="mt-3">
                        <div id="preview-content-form">Form content will appear here...</div>
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
                    <h6>Consent Form Guidelines:</h6>
                    <ul class="small text-muted">
                        <li>Use clear, simple language</li>
                        <li>Include all necessary information</li>
                        <li>Set realistic deadlines</li>
                        <li>Link to events when applicable</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6>Scope Options:</h6>
                    <ul class="small text-muted">
                        <li><strong>Section:</strong> Specific grade sections</li>
                        <li><strong>Class:</strong> Specific subject classes</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6>HTML Tags You Can Use:</h6>
                    <ul class="small text-muted">
                        <li>&lt;p&gt; for paragraphs</li>
                        <li>&lt;strong&gt; for bold text</li>
                        <li>&lt;em&gt; for italic text</li>
                        <li>&lt;ul&gt; and &lt;li&gt; for lists</li>
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
                        <a href="{{ route('teacher.consent-forms.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list me-2"></i>
                            All Forms
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus me-2"></i>
                            New Form
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
    
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const deadlineInput = document.getElementById('deadline');
    const formContentInput = document.getElementById('form_content');
    
    const previewTitle = document.getElementById('preview-title');
    const previewDescription = document.getElementById('preview-description');
    const previewScope = document.getElementById('preview-scope');
    const previewDeadline = document.getElementById('preview-deadline');
    const previewContentForm = document.getElementById('preview-content-form');
    
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
        }
        
        // Show/hide scope options container
        scopeOptions.style.display = (scope === '') ? 'none' : 'block';
        
        // Update preview
        updatePreview();
    });
    
    // Live preview updates
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    deadlineInput.addEventListener('change', updatePreview);
    formContentInput.addEventListener('input', updatePreview);
    scopeSelect.addEventListener('change', updatePreview);
    
    function updatePreview() {
        previewTitle.textContent = titleInput.value || 'Form Title';
        previewDescription.textContent = descriptionInput.value || 'Form description will appear here...';
        previewContentForm.innerHTML = formContentInput.value || 'Form content will appear here...';
        
        const scope = scopeSelect.value;
        const scopeText = scopeSelect.options[scopeSelect.selectedIndex]?.text || 'Scope';
        previewScope.textContent = scopeText.split(' ')[0];
        
        const deadline = deadlineInput.value;
        if (deadline) {
            const deadlineObj = new Date(deadline);
            previewDeadline.textContent = deadlineObj.toLocaleString();
        } else {
            previewDeadline.textContent = 'Deadline will appear here';
        }
    }
    
    // Initialize preview
    updatePreview();
    
    // Trigger scope change on load to show correct options
    scopeSelect.dispatchEvent(new Event('change'));
});

document.addEventListener('DOMContentLoaded', function() {
    const recipientType = document.getElementById('recipient_type');
    const recipientOptions = document.getElementById('recipient-options');
    const sectionSel = document.getElementById('section-selection');
    const classSel = document.getElementById('class-selection');
    const studentsSel = document.getElementById('students-selection');

    function updateRecipientOptions() {
        recipientOptions.style.display = 'block';
        sectionSel.style.display = 'none';
        classSel.style.display = 'none';
        studentsSel.style.display = 'none';
        if (recipientType.value === 'section') {
            sectionSel.style.display = 'block';
        } else if (recipientType.value === 'class') {
            classSel.style.display = 'block';
        } else if (recipientType.value === 'students') {
            studentsSel.style.display = 'block';
        } else {
            recipientOptions.style.display = 'none';
        }
    }
    recipientType.addEventListener('change', updateRecipientOptions);
    updateRecipientOptions();
});
</script>
@endpush 
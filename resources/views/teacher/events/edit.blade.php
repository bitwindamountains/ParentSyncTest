@extends('layouts.teacher')

@section('title', 'Edit Event - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Event</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.events.show', $event->event_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Event
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
                    Edit Event
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.events.update', $event->event_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $event->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="6" required>{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time" class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('time') is-invalid @enderror" 
                                       id="time" name="time" value="{{ old('time', $event->time) }}" required>
                                @error('time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location', $event->location) }}" required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Cost (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                           id="cost" name="cost" value="{{ old('cost', $event->cost) }}" step="0.01" min="0">
                                </div>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                                <select class="form-select @error('scope') is-invalid @enderror" id="scope" name="scope" required>
                                    <option value="">Select Scope</option>
                                    <option value="section" {{ old('scope', $event->scope) === 'section' ? 'selected' : '' }}>
                                        Section/Grade
                                    </option>
                                    <option value="class" {{ old('scope', $event->scope) === 'class' ? 'selected' : '' }}>
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
                                            {{ old('section_id', $event->section_id) == $section->section_id ? 'selected' : '' }}>
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
                                            {{ old('class_id', $event->class_id) == $class->class_id ? 'selected' : '' }}>
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
                        <label for="recipient_type" class="form-label">Recipient <span class="text-danger">*</span></label>
                        <select class="form-select @error('recipient_type') is-invalid @enderror" id="recipient_type" name="recipient_type" required>
                            <option value="">Select Recipient</option>
                            <option value="section" {{ old('recipient_type', $event->scope) === 'section' ? 'selected' : '' }}>Section/Grade</option>
                            <option value="class" {{ old('recipient_type', $event->scope) === 'class' ? 'selected' : '' }}>Class-wide</option>
                            <option value="students" {{ old('recipient_type', $event->scope) === 'students' ? 'selected' : '' }}>Specific Students</option>
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
                                    <option value="{{ $section->section_id }}" {{ old('section_id', $event->section_id) == $section->section_id ? 'selected' : '' }}>
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
                                    <option value="{{ $class->class_id }}" {{ old('class_id', $event->class_id) == $class->class_id ? 'selected' : '' }}>
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
                                    <option value="{{ $student->student_id }}" {{ (collect(old('student_ids', $event->participants->pluck('student_id')->toArray()))->contains($student->student_id)) ? 'selected' : '' }}>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organizer" class="form-label">Organizer (Optional)</label>
                                <input type="text" class="form-control @error('organizer') is-invalid @enderror" 
                                       id="organizer" name="organizer" value="{{ old('organizer', $event->organizer) }}">
                                @error('organizer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Information (Optional)</label>
                                <input type="text" class="form-control @error('contact_info') is-invalid @enderror" 
                                       id="contact_info" name="contact_info" value="{{ old('contact_info', $event->contact_info) }}">
                                @error('contact_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teacher.events.show', $event->event_id) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Update Event
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
                        <strong id="preview-title">Event Title</strong>
                    </div>
                    <div class="mb-3">
                        <span id="preview-scope" class="badge bg-secondary">Scope</span>
                    </div>
                    <div class="mb-3">
                        <strong>Date & Time:</strong><br>
                        <span id="preview-datetime" class="text-muted">Date and time will appear here</span>
                    </div>
                    <div class="mb-3">
                        <strong>Location:</strong><br>
                        <span id="preview-location" class="text-muted">Location will appear here</span>
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <div id="preview-description">Event description will appear here...</div>
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
                    <h6>Event Guidelines:</h6>
                    <ul class="small text-muted">
                        <li>Provide clear, detailed descriptions</li>
                        <li>Include all necessary information</li>
                        <li>Set appropriate scope for target audience</li>
                        <li>Add contact information if needed</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6>Scope Options:</h6>
                    <ul class="small text-muted">
                        <li><strong>General:</strong> All students and parents</li>
                        <li><strong>Section:</strong> Specific grade sections</li>
                        <li><strong>Class:</strong> Specific subject classes</li>
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
                        <a href="{{ route('teacher.events.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list me-2"></i>
                            All Events
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus me-2"></i>
                            New Event
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.create', ['event_id' => $event->event_id]) }}" 
                           class="btn btn-outline-warning w-100">
                            <i class="fas fa-file-signature me-2"></i>
                            Create Consent Form
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
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const locationInput = document.getElementById('location');
    
    const previewTitle = document.getElementById('preview-title');
    const previewDescription = document.getElementById('preview-description');
    const previewScope = document.getElementById('preview-scope');
    const previewDatetime = document.getElementById('preview-datetime');
    const previewLocation = document.getElementById('preview-location');
    
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
        scopeOptions.style.display = (scope === 'general') ? 'none' : 'block';
        
        // Update preview
        updatePreview();
    });
    
    // Live preview updates
    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    dateInput.addEventListener('change', updatePreview);
    timeInput.addEventListener('change', updatePreview);
    locationInput.addEventListener('input', updatePreview);
    scopeSelect.addEventListener('change', updatePreview);
    
    function updatePreview() {
        previewTitle.textContent = titleInput.value || 'Event Title';
        previewDescription.textContent = descriptionInput.value || 'Event description will appear here...';
        previewLocation.textContent = locationInput.value || 'Location will appear here';
        
        const scope = scopeSelect.value;
        const scopeText = scopeSelect.options[scopeSelect.selectedIndex]?.text || 'Scope';
        previewScope.textContent = scopeText.split(' ')[0];
        
        const date = dateInput.value;
        const time = timeInput.value;
        if (date && time) {
            const dateObj = new Date(date + 'T' + time);
            previewDatetime.textContent = dateObj.toLocaleString();
        } else {
            previewDatetime.textContent = 'Date and time will appear here';
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
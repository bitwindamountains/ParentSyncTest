@extends('layouts.teacher')

@section('title', $consentForm->title . ' - Consent Form Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $consentForm->title }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.consent-forms.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Consent Forms
            </a>
            <a href="{{ route('teacher.consent-forms.edit', $consentForm->form_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Consent Form Details -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-file-signature me-2"></i>
                    Consent Form Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Title:</strong><br>
                        <span class="text-muted">{{ $consentForm->title }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ optional($consentForm->created_at)->format('l, F d, Y \a\t g:i A') ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Deadline:</strong><br>
                        <span class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ optional($consentForm->deadline)->format('l, F d, Y \a\t g:i A') ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        @if($consentForm->deadline->isPast())
                            <span class="badge bg-danger">Expired</span>
                        @elseif($consentForm->deadline->diffInDays(now()) <= 3)
                            <span class="badge bg-warning text-dark">Due Soon</span>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Description:</strong><br>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($consentForm->description)) !!}
                    </div>
                </div>
                
                @if($consentForm->event)
                    <div class="mb-3">
                        <strong>Related Event:</strong><br>
                        <span class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $consentForm->event->title }} ({{ optional($consentForm->event->date)->format('M d, Y') ?? 'N/A' }})
                        </span>
                    </div>
                @endif
                
                @if($consentForm->section_id)
                    <div class="mb-3">
                        <strong>Target Section:</strong><br>
                        <span class="text-muted">
                            {{ $consentForm->section->section_name ?? 'N/A' }} 
                            (Grade {{ $consentForm->section->grade->grade_level ?? 'N/A' }})
                        </span>
                    </div>
                @elseif($consentForm->class_id)
                    <div class="mb-3">
                        <strong>Target Class:</strong><br>
                        <span class="text-muted">
                            {{ $consentForm->classRoom->class_name ?? 'N/A' }} - 
                            {{ $consentForm->classRoom->subject->subject_name ?? 'N/A' }}
                        </span>
                    </div>
                @endif
                
                @if($consentForm->attachment_path)
                    <div class="mb-3">
                        <strong>Attachment:</strong><br>
                        <a href="{{ asset('storage/' . $consentForm->attachment_path) }}" 
                           target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i>
                            Download Attachment
                        </a>
                    </div>
                @endif
                
                @if($consentForm->creator)
                    <div class="mb-3">
                        <strong>Created by:</strong><br>
                        <span class="text-muted">{{ $consentForm->creator->full_name ?? 'Unknown' }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="card shadow mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Form Content
                </h5>
            </div>
            <div class="card-body">
                <div class="border rounded p-4 bg-light">
                    {!! $consentForm->form_content !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Signatures Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-signature me-2"></i>
                    Signatures
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Total Signatures: {{ $signatures->count() }}</h6>
                </div>
                
                @if($signatures->count() > 0)
                    <div class="list-group">
                        @foreach($signatures->take(10) as $signature)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $signature->student->full_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $signature->parent->full_name ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">Signed</span><br>
                                    <small class="text-muted">
                                        {{ optional($signature->signed_at)->format('M d, Y') ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($signatures->count() > 10)
                            <div class="list-group-item text-center">
                                <small class="text-muted">
                                    And {{ $signatures->count() - 10 }} more signatures...
                                </small>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-muted">No signatures yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.edit', $consentForm->form_id) }}" 
                           class="btn btn-warning w-100">
                            <i class="fas fa-edit me-2"></i>
                            Edit Form
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.export', $consentForm->form_id) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>
                            Export Signatures
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.duplicate', $consentForm->form_id) }}" 
                           class="btn btn-info w-100">
                            <i class="fas fa-copy me-2"></i>
                            Duplicate Form
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form method="POST" action="{{ route('teacher.consent-forms.destroy', $consentForm->form_id) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this consent form?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>
                                Delete Form
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-compass me-2"></i>
                    Quick Navigation
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
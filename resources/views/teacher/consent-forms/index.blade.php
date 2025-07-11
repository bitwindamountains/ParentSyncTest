@extends('layouts.teacher')

@section('title', 'Consent Forms - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Consent Forms</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.consent-forms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Create Consent Form
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
                        <h4 class="mb-0">{{ $consentForms->total() }}</h4>
                        <small>Total Forms</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-signature fa-2x"></i>
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
                        <h4 class="mb-0">{{ $consentForms->where('deadline', '>=', now()->toDateString())->count() }}</h4>
                        <small>Active</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                    <h4 class="mb-0">{{ $consentForms->where('scope', 'section')->count() }}</h4>
                    <small>Section Forms</small>
                </div>
                <div class="align-self-center">
                    <i class="fas fa-users fa-2x"></i>
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
                        <h4 class="mb-0">{{ $consentForms->where('scope', 'class')->count() }}</h4>
                        <small>Class Forms</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consent Forms List -->
<div class="row">
    @if($consentForms->count() > 0)
        @foreach($consentForms as $form)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $form->title }}</h5>
                            @if($form->deadline->isPast())
                                <span class="badge bg-danger">Expired</span>
                            @elseif($form->deadline->diffInDays(now()) <= 3)
                                <span class="badge bg-warning text-dark">Due Soon</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            <span class="text-muted">{{ Str::limit($form->description, 100) }}</span>
                        </div>
                        
                        @if($form->event)
                            <div class="mb-3">
                                <strong>Related Event:</strong><br>
                                <span class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $form->event->title }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <strong>Deadline:</strong><br>
                            <span class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ optional($form->deadline)->format('l, F d, Y \a\t g:i A') ?? 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Scope:</strong><br>
                            @if($form->section_id)
                                <span class="badge bg-primary status-badge">
                                    Section: {{ $form->section->section_name ?? 'N/A' }}
                                </span>
                            @elseif($form->class_id)
                                <span class="badge bg-info status-badge">
                                    Class: {{ $form->classRoom->class_name ?? 'N/A' }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>Signatures:</strong><br>
                            <span class="text-muted">
                                {{ $form->signatures->count() }} signed
                            </span>
                        </div>
                        
                        @if($form->creator)
                            <div class="mb-3">
                                <strong>Created by:</strong><br>
                                <span class="text-muted">{{ $form->creator->full_name ?? 'Unknown' }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Created {{ $form->created_at->diffForHumans() }}
                            </small>
                            <div class="btn-group">
                                <a href="{{ route('teacher.consent-forms.show', $form->form_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>
                                    View
                                </a>
                                <a href="{{ route('teacher.consent-forms.edit', $form->form_id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('teacher.consent-forms.destroy', $form->form_id) }}" 
                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this consent form?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $consentForms->links() }}
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-signature fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Consent Forms Found</h4>
                    <p class="text-muted">You haven't created any consent forms yet.</p>
                    <a href="{{ route('teacher.consent-forms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create Your First Consent Form
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
                        <a href="{{ route('teacher.consent-forms.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Create Form
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Manage Events
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-bullhorn me-2"></i>
                            Announcements
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
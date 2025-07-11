@extends('layouts.teacher')

@section('title', $event->title . ' - Event Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $event->title }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Events
            </a>
            <a href="{{ route('teacher.events.edit', $event->event_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Event Details -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Event Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Title:</strong><br>
                        <span class="text-muted">{{ $event->title }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ $event->created_at ? $event->created_at->format('l, F d, Y \a\t g:i A') : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date & Time:</strong><br>
                        <span class="text-muted">
                            {{ $event->date->format('l, F d, Y') }}<br>
                            {{ $event->time }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Location:</strong><br>
                        <span class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $event->location }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Recipient:</strong><br>
                        <span class="badge bg-{{ $event->scope_color }} status-badge">
                            {{ $event->scope_display }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        @if($event->date->isPast())
                            <span class="badge bg-secondary">Past Event</span>
                        @elseif($event->date->isToday())
                            <span class="badge bg-success">Today</span>
                        @else
                            <span class="badge bg-info">Upcoming</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Description:</strong><br>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
                
                @if($event->scope === 'section' && $event->section)
                    <div class="mb-3">
                        <strong>Target Section:</strong><br>
                        <span class="text-muted">
                            {{ $event->section->section_name ?? 'N/A' }} 
                            (Grade {{ $event->section->grade->grade_level ?? 'N/A' }})
                        </span>
                    </div>
                @elseif($event->scope === 'class' && $event->classRoom)
                    <div class="mb-3">
                        <strong>Target Class:</strong><br>
                        <span class="text-muted">
                            {{ $event->classRoom->class_name ?? 'N/A' }} - 
                            {{ $event->classRoom->subject->subject_name ?? 'N/A' }}
                        </span>
                    </div>
                @elseif($event->scope === 'students')
                    <div class="mb-3">
                        <strong>Target:</strong><br>
                        <span class="text-muted">Specific Students</span>
                    </div>
                @endif
                
                @if($event->cost)
                    <div class="mb-3">
                        <strong>Cost:</strong><br>
                        <span class="text-muted">${{ number_format($event->cost, 2) }}</span>
                    </div>
                @endif
                
                @if($event->organizer)
                    <div class="mb-3">
                        <strong>Organizer:</strong><br>
                        <span class="text-muted">{{ $event->organizer }}</span>
                    </div>
                @endif
                
                @if($event->contact_info)
                    <div class="mb-3">
                        <strong>Contact Information:</strong><br>
                        <span class="text-muted">{{ $event->contact_info }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Participants Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Participants
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Total Participants: {{ $participants->count() }}</h6>
                </div>
                
                @if($participants->count() > 0)
                    <div class="list-group">
                        @foreach($participants->take(10) as $participant)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $participant->student->full_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $participant->student->section->section_name ?? 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge bg-success">Registered</span>
                            </div>
                        @endforeach
                        
                        @if($participants->count() > 10)
                            <div class="list-group-item text-center">
                                <small class="text-muted">
                                    And {{ $participants->count() - 10 }} more participants...
                                </small>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-muted">No participants registered yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Related Consent Forms -->
@if($event->consentForms->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-signature me-2"></i>
                        Related Consent Forms
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($event->consentForms as $form)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $form->title }}</h6>
                                        <p class="card-text small text-muted">{{ Str::limit($form->description, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Deadline: {{ $form->deadline->format('M d, Y') }}</small>
                                            <a href="{{ route('teacher.consent-forms.show', $form->form_id) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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
                        <a href="{{ route('teacher.events.edit', $event->event_id) }}" 
                           class="btn btn-warning w-100">
                            <i class="fas fa-edit me-2"></i>
                            Edit Event
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.create', ['event_id' => $event->event_id]) }}" 
                           class="btn btn-info w-100">
                            <i class="fas fa-file-signature me-2"></i>
                            Create Consent Form
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.duplicate', $event->event_id) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-copy me-2"></i>
                            Duplicate Event
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form method="POST" action="{{ route('teacher.events.destroy', $event->event_id) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>
                                Delete Event
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
                        <a href="{{ route('teacher.consent-forms.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-file-signature me-2"></i>
                            Consent Forms
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
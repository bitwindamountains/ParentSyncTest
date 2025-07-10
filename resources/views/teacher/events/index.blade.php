@extends('layouts.teacher')

@section('title', 'Events - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Events</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Create Event
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
                        <h4 class="mb-0">{{ $events->total() }}</h4>
                        <small>Total Events</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
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
                        <h4 class="mb-0">{{ $events->where('scope', 'section')->count() }}</h4>
                        <small>Section</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
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
                        <h4 class="mb-0">{{ $events->where('scope', 'class')->count() }}</h4>
                        <small>Class</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                        <h4 class="mb-0">{{ $events->where('scope', 'students')->count() }}</h4>
                        <small>Specific Students</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Events List -->
<div class="row">
    @if($events->count() > 0)
        @foreach($events as $event)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $event->title }}</h5>
                            <span class="badge bg-light text-dark">
                                {{ $event->date ? $event->date->format('M d') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Date & Time:</strong><br>
                            <span class="text-muted">
                                {{ $event->date ? $event->date->format('l, F d, Y') : 'N/A' }}<br>
                                {{ $event->time }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            <span class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $event->location }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            <span class="text-muted">{{ Str::limit($event->description, 100) }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Scope:</strong><br>
                            <span class="badge bg-{{ $event->scope_color }} status-badge">
                                {{ $event->scope_display }}
                            </span>
                        </div>
                        
                        @if($event->scope === 'section' && $event->section)
                            <div class="mb-3">
                                <strong>Section:</strong><br>
                                <span class="text-muted">
                                    {{ $event->section->section_name ?? 'N/A' }} 
                                    (Grade {{ $event->section->grade->grade_level ?? 'N/A' }})
                                </span>
                            </div>
                        @elseif($event->scope === 'class' && $event->classRoom)
                            <div class="mb-3">
                                <strong>Class:</strong><br>
                                <span class="text-muted">
                                    {{ $event->classRoom->class_name ?? 'N/A' }} - 
                                    {{ $event->classRoom->subject->subject_name ?? 'N/A' }}
                                </span>
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
                                <strong>Contact Info:</strong><br>
                                <span class="text-muted">{{ $event->contact_info }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                {{ $event->creator->full_name ?? 'Unknown' }}
                            </small>
                            @if($event->date->isPast())
                                <span class="badge bg-secondary">Past Event</span>
                            @elseif($event->date->isToday())
                                <span class="badge bg-success">Today</span>
                            @else
                                <span class="badge bg-info">Upcoming</span>
                            @endif
                        </div>
                        <div class="mt-2">
                            <div class="btn-group w-100">
                                <a href="{{ route('teacher.events.show', $event->event_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>
                                    View
                                </a>
                                <a href="{{ route('teacher.events.edit', $event->event_id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('teacher.events.destroy', $event->event_id) }}" 
                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
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
                {{ $events->links() }}
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted">You haven't created any events yet.</p>
                    <a href="{{ route('teacher.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create Your First Event
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
                        <a href="{{ route('teacher.events.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Create Event
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-file-signature me-2"></i>
                            Consent Forms
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.index') }}" class="btn btn-success w-100">
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
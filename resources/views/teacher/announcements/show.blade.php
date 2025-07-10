@extends('layouts.teacher')

@section('title', $announcement->title . ' - Announcement Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $announcement->title }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.announcements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Announcements
            </a>
            <a href="{{ route('teacher.announcements.edit', $announcement->announcement_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Announcement Details -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bullhorn me-2"></i>
                    Announcement Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Title:</strong><br>
                        <span class="text-muted">{{ $announcement->title }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ $announcement->created_at ? $announcement->created_at->format('l, F d, Y \a\t g:i A') : 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Scope:</strong><br>
                        <span class="badge bg-{{ $announcement->scope_color }} status-badge">
                            {{ $announcement->scope_display }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        @if($announcement->is_urgent)
                            <span class="badge bg-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Urgent
                            </span>
                        @else
                            <span class="badge bg-success">Normal</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Content:</strong><br>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
                
                @if($announcement->attachment_path)
                    <div class="mb-3">
                        <strong>Attachment:</strong><br>
                        <a href="{{ asset('storage/' . $announcement->attachment_path) }}" 
                           target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i>
                            Download Attachment
                        </a>
                    </div>
                @endif
                
                @if($announcement->scope === 'section' && $announcement->section)
                    <div class="mb-3">
                        <strong>Target Section:</strong><br>
                        <span class="text-muted">
                            {{ $announcement->section->section_name ?? 'N/A' }} 
                            (Grade {{ $announcement->section->grade->grade_level ?? 'N/A' }})
                        </span>
                    </div>
                @elseif($announcement->scope === 'class' && $announcement->classRoom)
                    <div class="mb-3">
                        <strong>Target Class:</strong><br>
                        <span class="text-muted">
                            {{ $announcement->classRoom->class_name ?? 'N/A' }} - 
                            {{ $announcement->classRoom->subject->subject_name ?? 'N/A' }}
                        </span>
                    </div>
                @elseif($announcement->scope === 'individual')
                    <div class="mb-3">
                        <strong>Target:</strong><br>
                        <span class="text-muted">Specific Students</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recipients Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Recipients
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Total Recipients: {{ $recipients->count() }}</h6>
                </div>
                
                @if($recipients->count() > 0)
                    <div class="list-group">
                        @foreach($recipients->take(10) as $recipient)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $recipient->student->full_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $recipient->student->section->section_name ?? 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge bg-success">Sent</span>
                            </div>
                        @endforeach
                        
                        @if($recipients->count() > 10)
                            <div class="list-group-item text-center">
                                <small class="text-muted">
                                    And {{ $recipients->count() - 10 }} more recipients...
                                </small>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-muted">No specific recipients for this announcement.</p>
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
                        <a href="{{ route('teacher.announcements.edit', $announcement->announcement_id) }}" 
                           class="btn btn-warning w-100">
                            <i class="fas fa-edit me-2"></i>
                            Edit Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.duplicate', $announcement->announcement_id) }}" 
                           class="btn btn-info w-100">
                            <i class="fas fa-copy me-2"></i>
                            Duplicate
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.announcements.export', $announcement->announcement_id) }}" 
                           class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>
                            Export Recipients
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form method="POST" action="{{ route('teacher.announcements.destroy', $announcement->announcement_id) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>
                                Delete
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
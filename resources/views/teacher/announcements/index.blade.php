@extends('layouts.teacher')

@section('title', 'Announcements - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Announcements</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.announcements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Create Announcement
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
                        <h4 class="mb-0">{{ $announcements->total() }}</h4>
                        <small>Total Announcements</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bullhorn fa-2x"></i>
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
                        <h4 class="mb-0">{{ $announcements->where('scope', 'section')->count() }}</h4>
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
                        <h4 class="mb-0">{{ $announcements->where('scope', 'class')->count() }}</h4>
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
                        <h4 class="mb-0">{{ $announcements->where('scope', 'individual')->count() }}</h4>
                        <small>Individual</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements List -->
<div class="row">
    @if($announcements->count() > 0)
        @foreach($announcements as $announcement)
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $announcement->title }}</h5>
                            <small class="text-muted">
                                Created {{ $announcement->created_at ? $announcement->created_at->diffForHumans() : 'No date' }}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-{{ $announcement->scope_color }} status-badge">
                                {{ $announcement->scope_display }}
                            </span>
                            @if($announcement->is_urgent)
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Urgent
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <strong>Content:</strong><br>
                                    <p class="text-muted">{{ Str::limit($announcement->content, 200) }}</p>
                                </div>
                                
                                @if($announcement->attachment_path)
                                    <div class="mb-3">
                                        <strong>Attachment:</strong><br>
                                        <a href="{{ asset('storage/' . $announcement->attachment_path) }}" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>
                                            Download Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Scope Details</h6>
                                        @if($announcement->scope === 'section' && $announcement->section_id)
                                            <p class="text-muted small">
                                                <strong>Section:</strong> {{ $announcement->section->section_name ?? 'N/A' }}<br>
                                                <strong>Grade:</strong> {{ $announcement->section->grade->grade_level ?? 'N/A' }}
                                            </p>
                                        @elseif($announcement->class_id)
                                            <p class="text-muted small">
                                                <strong>Class:</strong> {{ $announcement->classRoom->class_name ?? 'N/A' }}<br>
                                                <strong>Subject:</strong> {{ $announcement->classRoom->subject->subject_name ?? 'N/A' }}
                                            </p>
                                        @elseif($announcement->scope === 'individual')
                                            <p class="text-muted small">This announcement is for specific students.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i>
                                {{ $announcement->recipients->count() }} recipients
                            </small>
                            <div class="btn-group">
                                <a href="{{ route('teacher.announcements.show', $announcement->announcement_id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>
                                    View
                                </a>
                                <a href="{{ route('teacher.announcements.edit', $announcement->announcement_id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('teacher.announcements.destroy', $announcement->announcement_id) }}" 
                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
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
                {{ $announcements->links() }}
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Announcements Found</h4>
                    <p class="text-muted">You haven't created any announcements yet.</p>
                    <a href="{{ route('teacher.announcements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Create Your First Announcement
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
                        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Create Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.events.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Manage Events
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.consent-forms.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-file-signature me-2"></i>
                            Consent Forms
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
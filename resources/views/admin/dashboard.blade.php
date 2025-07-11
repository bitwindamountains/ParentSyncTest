@extends('layouts.app')

@section('title', 'Admin Dashboard - ParentSync')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Admin Dashboard
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.teachers.index') }}" class="text-decoration-none">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Teachers</h5>
                            <h2 class="mb-0">{{ $stats['teachers'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-4">
        <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Students</h5>
                            <h2 class="mb-0">{{ $stats['students'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>    
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Grades</h5>
                        <h2 class="mb-0">{{ $stats['grades'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Sections</h5>
                        <h2 class="mb-0">{{ $stats['sections'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.teachers.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus me-2"></i>
                            Add Teacher
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-user-graduate me-2"></i>
                            Add Student
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-info w-100">
                            <i class="fas fa-bullhorn me-2"></i>
                            Create Announcement
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-warning w-100">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Create Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bullhorn me-2"></i>
                    Recent Announcements
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">No announcements yet.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar me-2"></i>
                    Upcoming Events
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">No events scheduled.</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Manage Teachers</h5>
                    <p class="card-text">View and manage all teachers.</p>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-primary mt-auto">Go to Teachers</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Manage Students</h5>
                    <p class="card-text">View and manage all students.</p>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-primary mt-auto">Go to Students</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Manage Grade Levels</h5>
                    <p class="card-text">View and manage all grade levels and their sections.</p>
                    <a href="{{ route('admin.grades.index') }}" class="btn btn-primary mt-auto">Go to Grade Levels</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
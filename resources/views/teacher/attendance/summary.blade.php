@extends('layouts.teacher')

@section('title', 'Attendance Summary - Teacher Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Attendance Summary</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Attendance
            </a>
            <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history me-1"></i>
                History
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Summary Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.attendance.summary') }}" class="row g-3">
            <div class="col-md-3">
                <label for="date_from" class="form-label">From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3">
                <label for="section_id" class="form-label">Section</label>
                <select class="form-select" id="section_id" name="section_id">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->section_id }}" {{ $selectedSection == $section->section_id ? 'selected' : '' }}>
                            {{ $section->section_name }} (Grade {{ $section->grade->grade_level }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>
                    Generate Summary
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['total_days'] }}</h4>
                <small>Total Days</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['total_records'] }}</h4>
                <small>Total Records</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['present'] }}</h4>
                <small>Present</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['absent'] }}</h4>
                <small>Absent</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['late'] }}</h4>
                <small>Late</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $summary['excused'] }}</h4>
                <small>Excused</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Rate -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Overall Attendance Rate
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="text-primary mb-0">{{ $summary['attendance_rate'] }}%</h2>
                        <p class="text-muted mb-0">Overall attendance rate for the selected period</p>
                    </div>
                    <div class="col-md-6">
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($summary['present'] / max($summary['total_records'], 1)) * 100 }}%">
                                Present ({{ $summary['present'] }})
                            </div>
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ ($summary['late'] / max($summary['total_records'], 1)) * 100 }}%">
                                Late ({{ $summary['late'] }})
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ ($summary['absent'] / max($summary['total_records'], 1)) * 100 }}%">
                                Absent ({{ $summary['absent'] }})
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
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
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Take Attendance
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.attendance.history') }}" class="btn btn-info w-100">
                            <i class="fas fa-history me-2"></i>
                            View History
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button onclick="window.print()" class="btn btn-success w-100">
                            <i class="fas fa-print me-2"></i>
                            Print Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
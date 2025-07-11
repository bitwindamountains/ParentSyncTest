@extends('layouts.app')

@section('title', 'Manage Section')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.grades.index') }}">Grades</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.grades.sections.index', $section->grade_id) }}">Grade {{ $section->grade->grade_level }}</a></li>
            <li class="breadcrumb-item active">{{ $section->section_name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $section->section_name }}</h2>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
            <i class="fas fa-file-csv me-1"></i> Bulk Import
        </button>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    
    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bulkImportModalLabel"><i class="fas fa-file-csv me-2"></i>Bulk Import Students to Section (CSV)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('admin.sections.uploadCsv', $section->section_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label for="csv_file" class="form-label">CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                <br>
                <div class="form-text">Header must be: student_id</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Upload & Import</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <form action="{{ route('admin.sections.assignStudents', $section->section_id) }}" method="POST" class="mb-4">
        @csrf
        <h4>Assign Students Manually</h4>
        <div class="mb-3">
            <label>Select students to add to this section:</label>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Student ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students->where('section_id', null) as $student)
                            <tr>
                                <td><input type="checkbox" name="student_ids[]" value="{{ $student->student_id }}"></td>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->full_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Assign Selected Students</button>
    </form>
    <h4>Current Roster</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($section->students as $student)
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->full_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 
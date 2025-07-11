@extends('layouts.app')

@section('title', 'Manage Sections')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.grades.index') }}">Grades</a></li>
            <li class="breadcrumb-item active">Grade {{ $grade->grade_level }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Sections for Grade {{ $grade->grade_level }}</h2>
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
    <form action="{{ route('admin.grades.sections.store', $grade->grade_id) }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="section_name" class="form-label">Add Section</label>
                <input type="text" name="section_name" id="section_name" class="form-control @error('section_name') is-invalid @enderror" value="{{ old('section_name') }}" required>
                @error('section_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success">Add Section</button>
            </div>
        </div>
    </form>
    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bulkImportModalLabel"><i class="fas fa-file-csv me-2"></i>Bulk Import Students to Sections (CSV)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('admin.grades.sections.uploadCsv', $grade->grade_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label for="csv_file" class="form-label">CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                <br>
                <div class="form-text">Header must be: student_id,section_name</div>
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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Section Name</th>
                <th>Adviser</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $section)
                <tr>
                    <td>{{ $section->section_name }}</td>
                    <td>{{ $section->teacher ? $section->teacher->full_name : 'No adviser assigned' }}</td>
                    <td>
                        <a href="{{ route('admin.sections.show', $section->section_id) }}" class="btn btn-info btn-sm">Manage</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 
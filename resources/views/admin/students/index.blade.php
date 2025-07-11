@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Students</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Add Student</a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                <i class="fas fa-file-csv me-1"></i> Bulk Import
            </button>
        </div>
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
            <h5 class="modal-title" id="bulkImportModalLabel"><i class="fas fa-file-csv me-2"></i>Bulk Import Students (CSV)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('admin.students.uploadCsv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label for="csv_file" class="form-label">CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                <br>
                <div class="form-text">Header must be: student_id,first_name,last_name,birthdate,grade_level,section_id</div>
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

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birthdate</th>
                <th>Grade Level</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->birthdate->format('m/d/Y') }}</td>
                    <td>{{ $student->grade_level }}</td>
                    <td>{{ $student->section->section_name ?? ' ' }}</td>
                    <td>
                        <a href="{{ route('admin.students.show', $student->student_id) }}" class="btn btn-sm btn-info">Profile</a>
                        <a href="{{ route('admin.students.edit', $student->student_id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.students.destroy', $student->student_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $students->links() }}
</div>
@endsection 
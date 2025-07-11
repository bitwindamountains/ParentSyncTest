@extends('layouts.app')

@section('title', 'Manage Teachers')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Teachers</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Add Teacher</a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                <i class="fas fa-file-csv me-1"></i> Bulk Import
            </button>
        </div>
    </div>

    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkImportModalLabel"><i class="fas fa-file-csv me-2"></i>Bulk Import Teachers (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.teachers.uploadCsv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                <div class="mb-3">
                    <label for="csv_file" class="form-label">CSV File</label>
                    <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                    <br>
                    <div class="form-text">Header must be: teacher_id,first_name,last_name,email,contactNo</div>
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
                <th>Email</th>
                <th>Contact No.</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->teacher_id }}</td>
                    <td>{{ $teacher->first_name }}</td>
                    <td>{{ $teacher->last_name }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td>{{ $teacher->contactNo }}</td>
                    <td>
                        <a href="{{ route('admin.teachers.show', $teacher->teacher_id) }}" class="btn btn-sm btn-info">Profile</a>
                        <a href="{{ route('admin.teachers.edit', $teacher->teacher_id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.teachers.destroy', $teacher->teacher_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $teachers->links() }}
</div>
@endsection 
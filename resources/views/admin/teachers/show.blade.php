@extends('layouts.app')

@section('title', 'Teacher Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Teacher Profile</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Teacher ID:</strong></td>
                                    <td>{{ $teacher->teacher_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td>{{ $teacher->first_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ $teacher->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $teacher->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contact No:</strong></td>
                                    <td>{{ $teacher->contactNo ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Account Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>{{ $teacher->user->username }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td><span class="badge bg-primary">{{ $teacher->user->role }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Status:</strong></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            </table>
                            <div class="text-center mt-4">
                                <i class="fas fa-chalkboard-teacher fa-5x text-primary mb-3"></i>
                                <h4>{{ $teacher->first_name }} {{ $teacher->last_name }}</h4>
                                <p class="text-muted">Teacher</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.teachers.edit', $teacher->teacher_id) }}" class="btn btn-warning">Edit Teacher</a>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
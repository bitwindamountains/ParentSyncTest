@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Student Profile</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Student ID:</strong></td>
                                    <td>{{ $student->student_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td>{{ $student->first_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ $student->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Birthdate:</strong></td>
                                    <td>{{ $student->birthdate }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($student->birthdate)->age }} years old</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <i class="fas fa-user-graduate fa-5x text-primary mb-3"></i>
                                <h4>{{ $student->first_name }} {{ $student->last_name }}</h4>
                                <p class="text-muted">Student</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.students.edit', $student->student_id) }}" class="btn btn-warning">Edit Student</a>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
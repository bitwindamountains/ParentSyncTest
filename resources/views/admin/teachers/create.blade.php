@extends('layouts.app')

@section('title', 'Add Teacher')

@section('content')
<div class="container">
    <h2>Add Teacher</h2>
    <form action="{{ route('admin.teachers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Teacher ID</label>
            <input type="text" class="form-control @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" value="{{ old('teacher_id') }}" required>
            @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="contactNo" class="form-label">Contact No</label>
            <input type="text" class="form-control @error('contactNo') is-invalid @enderror" id="contactNo" name="contactNo" value="{{ old('contactNo') }}">
            @error('contactNo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="text" class="form-control" id="password" name="password" value="TempPass123" disabled>
            <div class="form-text">Default password is <b>TempPass123</b></div>
        </div>
        <button type="submit" class="btn btn-primary">Add Teacher</button>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 
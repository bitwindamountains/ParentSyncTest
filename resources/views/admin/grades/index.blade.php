@extends('layouts.app')

@section('title', 'Manage Grades')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Grades</li>
        </ol>
    </nav>
    <h2>Grade Levels</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
                <tr>
                    <td>{{ $grade->grade_level }}</td>
                    <td>
                        <a href="{{ route('admin.grades.sections.index', $grade->grade_id) }}" class="btn btn-primary btn-sm">Manage Sections</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 
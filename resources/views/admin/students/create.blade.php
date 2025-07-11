@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
<div class="container">
    <h2>Add Student</h2>
    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input type="text" class="form-control @error('student_id') is-invalid @enderror" id="student_id" name="student_id" value="{{ old('student_id') }}" required>
            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <label for="birthdate" class="form-label">Birthdate</label>
            <input type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" required>
            @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="grade_level" class="form-label">Grade Level</label>
            <select class="form-control @error('grade_level') is-invalid @enderror" id="grade_level" name="grade_level" required>
                <option value="">Select Grade</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->grade_level }}" {{ old('grade_level') == $grade->grade_level ? 'selected' : '' }}>{{ $grade->grade_level }}</option>
                @endforeach
            </select>
            @error('grade_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3" id="section-group" style="display: none;">
            <label for="section_id" class="form-label">Section (optional)</label>
            <select class="form-control @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                <option value="">No Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section->section_id }}" data-grade="{{ $section->grade->grade_level }}" style="display:none;"
                        {{ old('section_id') == $section->section_id ? 'selected' : '' }}>
                        {{ $section->section_name }}
                    </option>
                @endforeach
            </select>
            @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gradeSelect = document.getElementById('grade_level');
                const sectionGroup = document.getElementById('section-group');
                const sectionSelect = document.getElementById('section_id');
                const allSectionOptions = Array.from(sectionSelect.options);

                function filterSections() {
                    const selectedGrade = gradeSelect.value;
                    let hasSection = false;
                    allSectionOptions.forEach(option => {
                        if (!option.value) return; // skip 'No Section'
                        if (option.getAttribute('data-grade') === selectedGrade) {
                            option.style.display = '';
                            hasSection = true;
                        } else {
                            option.style.display = 'none';
                            if (option.selected) option.selected = false;
                        }
                    });
                    sectionGroup.style.display = selectedGrade ? '' : 'none';
                }

                gradeSelect.addEventListener('change', filterSections);
                filterSections();
            });
        </script>
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 
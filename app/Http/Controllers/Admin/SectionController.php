<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index($gradeId)
    {
        $grade = Grade::findOrFail($gradeId);
        $sections = $grade->sections()->with('teacher')->orderBy('section_name')->get();
        return view('admin.sections.index', compact('grade', 'sections'));
    }

    public function store(Request $request, $gradeId)
    {
        $grade = Grade::findOrFail($gradeId);
        $request->validate([
            'section_name' => 'required|string|unique:sections,section_name,NULL,section_id,grade_id,' . $gradeId,
        ]);
        $section = $grade->sections()->create([
            'section_name' => $request->section_name,
        ]);
        return redirect()->route('admin.grades.sections.index', $gradeId)->with('success', 'Section added successfully.');
    }

    public function show($sectionId)
    {
        $section = Section::with(['teacher', 'students'])->findOrFail($sectionId);
        $teachers = Teacher::orderBy('last_name')->get();
        $students = Student::where('grade_level', $section->grade->grade_level)->get();
        return view('admin.sections.show', compact('section', 'teachers', 'students'));
    }

    public function uploadGradeCsv(Request $request, $gradeId)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        $grade = Grade::with('sections')->findOrFail($gradeId);
        $sectionsByName = $grade->sections->keyBy(function($section) {
            return strtolower(trim($section->section_name));
        });
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $expected = ['student_id', 'section_name'];
        if (array_map('strtolower', $header) !== $expected) {
            return back()->with('error', 'CSV header must be: student_id,section_name');
        }
        $success = 0;
        $errors = [];
        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = array_combine($expected, $row);
            $sectionKey = strtolower(trim($data['section_name']));
            $section = $sectionsByName[$sectionKey] ?? null;
            if (!$section) {
                $errors[] = "Row $rowNum: Section '{$data['section_name']}' not found in this grade.";
                continue;
            }
            $student = Student::where('student_id', $data['student_id'])
                ->where('grade_level', $grade->grade_level)
                ->first();
            if (!$student) {
                $errors[] = "Row $rowNum: Student not found or not in this grade.";
                continue;
            }
            $student->section_id = $section->section_id;
            $student->save();
            $success++;
        }
        fclose($handle);
        $msg = "$success students assigned to sections.";
        if ($errors) {
            $msg .= ' Some rows failed: ' . implode(' | ', $errors);
            return back()->with('error', $msg);
        }
        return back()->with('success', $msg);
    }

    public function uploadSectionCsv(Request $request, $sectionId)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        $section = Section::findOrFail($sectionId);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $expected = ['student_id'];
        if (array_map('strtolower', $header) !== $expected) {
            return back()->with('error', 'CSV header must be: student_id');
        }
        $success = 0;
        $errors = [];
        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = array_combine($expected, $row);
            $student = Student::where('student_id', $data['student_id'])
                ->where('grade_level', $section->grade->grade_level)
                ->first();
            if (!$student) {
                $errors[] = "Row $rowNum: Student not found or not in this grade.";
                continue;
            }
            $student->section_id = $section->section_id;
            $student->save();
            $success++;
        }
        fclose($handle);
        $msg = "$success students assigned to section.";
        if ($errors) {
            $msg .= ' Some rows failed: ' . implode(' | ', $errors);
            return back()->with('error', $msg);
        }
        return back()->with('success', $msg);
    }

    public function assignStudents(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'numeric|exists:students,student_id',
        ]);
        $students = Student::whereIn('student_id', $request->student_ids)
            ->where('grade_level', $section->grade->grade_level)
            ->whereNull('section_id')
            ->get();
        foreach ($students as $student) {
            $student->section_id = $section->section_id;
            $student->save();
        }
        return back()->with('success', count($students) . ' students assigned to section.');
    }

    public function assignAdviser(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);
        $request->validate([
            'teacher_id' => 'required|numeric|exists:teachers,teacher_id',
        ]);
        $section->teacher_id = $request->teacher_id;
        $section->save();
        return back()->with('success', 'Adviser assigned to section.');
    }
    
} 
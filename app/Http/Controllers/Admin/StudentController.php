<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::paginate(10);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $grades = \App\Models\Grade::orderBy('grade_level')->get();
        $sections = \App\Models\Section::orderBy('section_name')->get();
        return view('admin.students.create', compact('grades', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|numeric|unique:students,student_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'required|date',
            'grade_level' => 'required|numeric',
            'section_id' => 'nullable|numeric|exists:sections,section_id',
        ]);

        $student = Student::create([
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthdate' => $request->birthdate,
            'grade_level' => $request->grade_level,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student added successfully.');
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'required|date',
        ]);

        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthdate' => $request->birthdate,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }
    
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $expected = ['student_id', 'first_name', 'last_name', 'birthdate', 'grade_level', 'section_id'];
        if (array_map('strtolower', $header) !== $expected) {
            return back()->with('error', 'CSV header must be: student_id,first_name,last_name,birthdate,grade_level,section_id');
        }

        $success = 0;
        $errors = [];
        $rowNum = 1;
        
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = array_combine($expected, $row);
            
            if (empty($data['section_id'])) {
                $data['section_id'] = null;
            }
            $validator = Validator::make($data, [
                'student_id' => 'required|numeric|unique:students,student_id',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'birthdate' => 'required|date',
                'grade_level' => 'required|numeric',
                'section_id' => 'nullable|numeric|exists:sections,section_id',
            ]);
            if ($validator->fails()) {
                $errors[] = "Row $rowNum: " . implode('; ', $validator->errors()->all());
                continue;
            }
            try {
                Student::create($data);
                $success++;
            } catch (Exception $e) {
                $errors[] = "Row $rowNum: " . $e->getMessage();
            }
        }
        fclose($handle);
        $msg = "$success students imported.";
        if ($errors) {
            $msg .= ' Some rows failed: ' . implode(' | ', $errors);
            return back()->with('error', $msg);
        }
        return back()->with('success', $msg);
    }
}
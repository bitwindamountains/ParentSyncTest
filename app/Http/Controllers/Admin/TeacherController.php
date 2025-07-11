<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|numeric|unique:teachers,teacher_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:teachers,email',
            'contactNo' => 'nullable|string',
        ]);

        $user = User::create([
            'username' => $request->teacher_id,
            'password_hash' => \Hash::make('TempPass123'),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'teacher_id' => $request->teacher_id,
            'user_id' => $user->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contactNo' => $request->contactNo,
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $request->validate([
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:teachers,email,' . $teacher->teacher_id . ',teacher_id',
            'contactNo' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $teacher->update($request->only(['first_name', 'last_name', 'email', 'contactNo']));
        if ($request->filled('password')) {
            $user->password_hash = \Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;
        $teacher->delete();
        if ($user) {
            $user->delete();
        }
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $expected = ['teacher_id', 'first_name', 'last_name', 'email', 'contactno'];
        if (array_map('strtolower', $header) !== $expected) {
            return back()->with('error', 'CSV header must be: teacher_id,first_name,last_name,email,contactno');
        }

        $success = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $data = array_combine($expected, $row);
            
            if (empty($data['contactno'])) {
                $data['contactno'] = null;
            }
            $validator = Validator::make($data, [
                'teacher_id' => 'required|numeric|unique:teachers,teacher_id',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:teachers,email',
                'contactno' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                $errors[] = "Row $rowNum: " . implode('; ', $validator->errors()->all());
                continue;
            }
            try {
                $user = User::create([
                    'username' => $data['teacher_id'],
                    'password_hash' => Hash::make('TempPass123'), 
                    'role' => 'teacher',
                ]);
                Teacher::create([
                    'teacher_id' => $data['teacher_id'],
                    'user_id' => $user->user_id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'contactNo' => $data['contactno'],
                ]);
                $success++;
            } catch (Exception $e) {
                $errors[] = "Row $rowNum: " . $e->getMessage();
            }
        }
        fclose($handle);
        $msg = "$success teachers imported.";
        if ($errors) {
            $msg .= ' Some rows failed: ' . implode(' | ', $errors);
            return back()->with('error', $msg);
        }
        return back()->with('success', $msg);
    }
}
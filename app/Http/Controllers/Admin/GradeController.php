<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::orderBy('grade_level')->get();
        return view('admin.grades.index', compact('grades'));
    }
} 
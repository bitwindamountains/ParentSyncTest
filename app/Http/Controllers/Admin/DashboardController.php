<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\School;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => Teacher::count(),
            'students' => Student::count(),
            'grades' => Grade::count(),
            'sections' => Section::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
} 


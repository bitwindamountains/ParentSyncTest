<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\ConsentForm;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware;
use App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
    }

    public function dashboard()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Teacher account not found.');
        }

        $data = [
            'teacher' => $teacher,
            'sections' => $teacher->sections()->with(['students', 'grade'])->get(),
            'classes' => $teacher->classes()->with(['section.students', 'subject'])->get(),
            'totalStudents' => $teacher->getStudents()->count(),
            'recentAnnouncements' => Announcement::where('created_by', Auth::id())
                ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
                ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
                ->latest()
                ->take(5)
                ->get(),
            'upcomingEvents' => Event::where('created_by', Auth::id())
                ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
                ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->take(5)
                ->get(),
            'pendingConsentForms' => ConsentForm::where('created_by', Auth::id())
                ->where('deadline', '>=', now()->toDateString())
                ->with(['event', 'section', 'classRoom'])
                ->take(5)
                ->get(),
        ];

        return view('teacher.dashboard', $data);
    }

    public function myClasses()
    {
        $teacher = Auth::user()->teacher;
        $classes = $teacher->classes()->with(['section.students', 'subject'])->get();
        
        return view('teacher.classes.index', compact('classes'));
    }

    public function myStudents()
    {
        $teacher = Auth::user()->teacher;
        $students = $teacher->getStudents()->load(['section.grade']);
        
        return view('teacher.students.index', compact('students'));
    }

    public function classDetails($classId)
    {
        $teacher = Auth::user()->teacher;
        $class = $teacher->classes()->with(['section.students', 'subject'])->findOrFail($classId);
        
        return view('teacher.classes.show', compact('class'));
    }

    public function sectionDetails($sectionId)
    {
        $teacher = Auth::user()->teacher;
        $section = $teacher->sections()->with(['students.grade', 'grade'])->findOrFail($sectionId);
        
        return view('teacher.sections.show', compact('section'));
    }
} 
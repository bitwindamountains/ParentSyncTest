<?php

namespace App\Http\Controllers\Teacher;

use App\Models\AttendanceRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
    }

    // Step 1: Show attendance marking UI (date, grade, section, subject selection, students)
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $date = $request->input('date', Carbon::now()->toDateString());
        $gradeId = $request->input('grade_id');
        $sectionId = $request->input('section_id');
        $search = $request->input('search');

        // Get teacher's assigned grades and sections
        $grades = Grade::whereHas('sections', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->teacher_id);
        })->get();

        $sections = collect();
        $students = collect();
        $selectedGrade = null;
        $selectedSection = null;

        if ($gradeId) {
            $selectedGrade = $grades->where('grade_id', $gradeId)->first();
            if ($selectedGrade) {
                $sections = $selectedGrade->sections()->where('teacher_id', $teacher->teacher_id)->get();
            }
        }

        if ($sectionId) {
            $selectedSection = $sections->where('section_id', $sectionId)->first();
        }

        if ($sectionId) {
            // Get students from the selected section
            $students = Student::where('section_id', $sectionId)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
            
            // Apply search filter if provided
            if ($search) {
                $students = $students->filter(function($student) use ($search) {
                    return stripos($student->last_name, $search) !== false || 
                           stripos($student->first_name, $search) !== false;
                });
            }
        }

        // Existing attendance for this date/section
        $attendanceRecords = collect();
        if ($students->count() > 0) {
            $attendanceRecords = AttendanceRecord::whereIn('student_id', $students->pluck('student_id'))
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.index', [
            'grades' => $grades,
            'sections' => $sections,
            'students' => $students,
            'attendanceRecords' => $attendanceRecords,
            'selectedDate' => $date,
            'selectedGrade' => $selectedGrade,
            'selectedSection' => $selectedSection,
            'search' => $search,
        ]);
    }

    // Step 2: Store attendance
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,student_id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'section_id' => 'required|exists:sections,section_id',
        ]);

        $teacher = Auth::user()->teacher;
        
        // Verify teacher has access to selected section
        $section = Section::where('section_id', $request->section_id)
            ->where('teacher_id', $teacher->teacher_id)
            ->first();
        
        if (!$section) {
            return back()->with('error', 'Unauthorized section.');
        }

        DB::transaction(function () use ($request, $teacher) {
            foreach ($request->attendance as $entry) {
                AttendanceRecord::updateOrCreate(
                    [
                        'student_id' => $entry['student_id'],
                        'date' => $request->date,
                    ],
                    [
                        'section_id' => $request->section_id,
                        'status' => $entry['status'],
                        'notes' => $entry['notes'] ?? null,
                        'marked_by' => $teacher->teacher_id,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Attendance saved successfully.');
    }

    // Step 3: Attendance history (filterable)
    public function history(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $grades = Grade::whereHas('sections', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->teacher_id);
        })->orWhereHas('sections.classes', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->teacher_id);
        })->get();

        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $gradeId = $request->input('grade_id');
        $sectionId = $request->input('section_id');
        $subjectId = $request->input('subject_id');
        $studentId = $request->input('student_id');
        $status = $request->input('status');

        $sections = collect();
        $subjects = collect();
        $students = collect();

        if ($gradeId) {
            $grade = $grades->where('grade_id', $gradeId)->first();
            if ($grade) {
                $sections = $grade->sections()->where('teacher_id', $teacher->teacher_id)->get();
            }
        }

        if ($sectionId) {
            $section = $sections->where('section_id', $sectionId)->first();
            if ($section) {
                $subjects = Subject::whereHas('classes', function($query) use ($sectionId, $teacher) {
                    $query->where('section_id', $sectionId)
                          ->where('teacher_id', $teacher->teacher_id);
                })->get();
                $students = $section->students;
            }
        }

        $query = AttendanceRecord::query()->with(['student', 'section', 'subject']);
        $query->whereBetween('date', [$dateFrom, $dateTo]);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        
        $attendanceRecords = $query->orderBy('date', 'desc')->paginate(30);

        return view('teacher.attendance.history', [
            'grades' => $grades,
            'sections' => $sections,
            'subjects' => $subjects,
            'students' => $students,
            'attendanceRecords' => $attendanceRecords,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedGrade' => $gradeId,
            'selectedSection' => $sectionId,
            'selectedSubject' => $subjectId,
            'selectedStudent' => $studentId,
            'selectedStatus' => $status,
        ]);
    }

    // Step 4: Attendance summary
    public function summary(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $sectionId = $request->input('section_id');

        $sections = $teacher->sections()->with('grade')->get();

        $query = AttendanceRecord::with(['student', 'section'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        } else {
            $query->whereIn('section_id', $teacher->sections->pluck('section_id'));
        }

        $records = $query->get();

        $summary = [
            'total_days' => $records->unique('date')->count(),
            'total_records' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
        ];

        $summary['attendance_rate'] = $summary['total_records'] > 0 
            ? round((($summary['present'] + $summary['late']) / $summary['total_records']) * 100, 2)
            : 0;

        return view('teacher.attendance.summary', [
            'sections' => $sections,
            'summary' => $summary,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedSection' => $sectionId,
        ]);
    }

    // AJAX method to get sections for a grade
    public function getSections($gradeId)
    {
        $teacher = Auth::user()->teacher;
        
        $sections = Section::where('grade_id', $gradeId)
            ->where('teacher_id', $teacher->teacher_id)
            ->get();
        
        return response()->json($sections);
    }
}

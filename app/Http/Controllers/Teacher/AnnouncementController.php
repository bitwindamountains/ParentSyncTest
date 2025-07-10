<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
    }

    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $announcements = Announcement::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['creator', 'section', 'classRoom', 'recipients.student'])
            ->latest()
            ->paginate(15);
        
        return view('teacher.announcements.index', compact('announcements'));
    }

    public function create()
    { 
        $teacher = Auth::user()->teacher;
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        $students = $teacher->getStudents();
        
        return view('teacher.announcements.create', compact('sections', 'classes', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:section,class,individual',
            'section_id' => 'nullable|sometimes|required_if:scope,section|exists:Sections,section_id',
            'class_id' => 'nullable|sometimes|required_if:scope,class|exists:Classes,class_id',
            'student_ids' => 'required_if:scope,individual|array',
            'student_ids.*' => 'exists:Students,student_id',
        ]);

        $teacher = Auth::user()->teacher;
        // Verify teacher has access to selected section/class
        if ($request->scope === 'section' && $request->section_id && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->withInput()->with('error', 'Unauthorized access to section.');
        }
        if ($request->scope === 'class' && $request->class_id && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->withInput()->with('error', 'Unauthorized access to class.');
        }

        DB::transaction(function () use ($request) {
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'created_by' => Auth::id(),
                'scope' => $request->scope,
                'section_id' => $request->section_id,
                'class_id' => $request->class_id,
            ]);

            $recipientIds = [];
            if ($request->scope === 'section') {
                $recipientIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
            } elseif ($request->scope === 'class') {
                $class = \App\Models\Classes::with('section')->find($request->class_id);
                if ($class) {
                    $recipientIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
                }
            } elseif ($request->scope === 'individual' && $request->student_ids) {
                $recipientIds = $request->student_ids;
            }
            foreach ($recipientIds as $studentId) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->announcement_id,
                    'student_id' => $studentId,
                ]);
            }
        });

        return redirect()->route('teacher.announcements.index')->with('success', 'Announcement created successfully!');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        $announcement = Announcement::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['creator', 'section', 'classRoom', 'recipients.student'])
            ->findOrFail($id);
        
        return view('teacher.announcements.show', compact('announcement'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        
        $announcement = Announcement::where('created_by', Auth::id())->findOrFail($id);
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        $students = $teacher->getStudents();
        
        return view('teacher.announcements.edit', compact('announcement', 'sections', 'classes', 'students'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:section,class,individual',
            'section_id' => 'nullable|sometimes|required_if:scope,section|exists:Sections,section_id',
            'class_id' => 'nullable|sometimes|required_if:scope,class|exists:Classes,class_id',
            'student_ids' => 'required_if:scope,individual|array',
            'student_ids.*' => 'exists:Students,student_id',
        ]);

        $teacher = Auth::user()->teacher;
        $announcement = Announcement::where('created_by', Auth::id())->findOrFail($id);
        // Verify teacher has access to selected section/class
        if ($request->scope === 'section' && $request->section_id && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->withInput()->with('error', 'Unauthorized access to section.');
        }
        if ($request->scope === 'class' && $request->class_id && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->withInput()->with('error', 'Unauthorized access to class.');
        }

        DB::transaction(function () use ($request, $announcement) {
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'scope' => $request->scope,
                'section_id' => $request->section_id,
                'class_id' => $request->class_id,
            ]);

            $recipientIds = [];
            if ($request->scope === 'section') {
                $recipientIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
            } elseif ($request->scope === 'class') {
                $class = \App\Models\Classes::with('section')->find($request->class_id);
                if ($class) {
                    $recipientIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
                }
            } elseif ($request->scope === 'individual' && $request->student_ids) {
                $recipientIds = $request->student_ids;
            }
            // Remove old recipients and add new
            $announcement->recipients()->delete();
            foreach ($recipientIds as $studentId) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->announcement_id,
                    'student_id' => $studentId,
                ]);
            }
        });

        return redirect()->route('teacher.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::where('created_by', Auth::id())->findOrFail($id);
        
        DB::transaction(function () use ($announcement) {
            $announcement->recipients()->delete();
            $announcement->delete();
        });

        return redirect()->route('teacher.announcements.index')->with('success', 'Announcement deleted successfully!');
    }

    public function getStudentsByScope(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $scope = $request->scope;
        $sectionId = $request->section_id;
        $classId = $request->class_id;
        
        $students = collect();
        
        switch ($scope) {
            case 'section':
                if ($teacher->sections->contains('section_id', $sectionId)) {
                    $students = Section::find($sectionId)->students;
                }
                break;
            case 'class':
                if ($teacher->classes->contains('class_id', $classId)) {
                    $students = Classes::find($classId)->getStudents();
                }
                break;
            case 'individual':
                $students = $teacher->getStudents();
                break;
        }
        
        return response()->json($students);
    }

    public function duplicate($id)
    {
        $teacher = Auth::user()->teacher;
        
        $announcement = Announcement::where('created_by', Auth::id())->findOrFail($id);
        
        $newAnnouncement = $announcement->replicate();
        $newAnnouncement->title = $announcement->title . ' (Copy)';
        $newAnnouncement->created_at = now();
        $newAnnouncement->save();
        
        // Copy recipients
        foreach ($announcement->recipients as $recipient) {
            AnnouncementRecipient::create([
                'announcement_id' => $newAnnouncement->announcement_id,
                'student_id' => $recipient->student_id,
            ]);
        }
        
        return redirect()->route('teacher.announcements.edit', $newAnnouncement->announcement_id)
            ->with('success', 'Announcement duplicated successfully!');
    }

    public function export($id)
    {
        $teacher = Auth::user()->teacher;
        
        $announcement = Announcement::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['recipients.student'])
            ->findOrFail($id);
        
        $recipients = $announcement->recipients;
        
        // Generate CSV
        $filename = 'announcement_recipients_' . $announcement->announcement_id . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($recipients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Section', 'Status']);
            
            foreach ($recipients as $recipient) {
                fputcsv($file, [
                    $recipient->student->full_name ?? 'N/A',
                    $recipient->student->section->section_name ?? 'N/A',
                    'Sent',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 
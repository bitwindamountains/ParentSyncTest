<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Event;
use App\Models\Section;
use App\Models\Classes;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
    }

    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $events = Event::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['creator', 'section', 'classRoom', 'participants.student'])
            ->latest()
            ->paginate(15);
        
        return view('teacher.events.index', compact('events'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        $students = $teacher->getStudents();
        return view('teacher.events.create', compact('sections', 'classes', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'scope' => 'required|in:class,grade,specific_students',
            'section_id' => 'required_if:scope,grade|nullable|exists:Sections,section_id',
            'class_id' => 'required_if:scope,class|nullable|exists:Classes,class_id',
            'student_ids' => 'required_if:scope,specific_students|array',
            'student_ids.*' => 'exists:Students,student_id',
        ]);

        $teacher = Auth::user()->teacher;
        // Verify teacher has access to selected section/class
        if ($request->scope === 'grade' && $request->section_id && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->with('error', 'Unauthorized access to section.');
        }
        if ($request->scope === 'class' && $request->class_id && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->with('error', 'Unauthorized access to class.');
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'cost' => $request->cost ?? 0,
            'created_by' => Auth::id(),
            'scope' => $request->scope,
            'section_id' => $request->section_id,
            'class_id' => $request->class_id,
        ]);

        // Assign participants
        $participantIds = [];
        if ($request->scope === 'grade') {
            $participantIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
        } elseif ($request->scope === 'class') {
            $class = \App\Models\Classes::with('section')->find($request->class_id);
            if ($class) {
                $participantIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
            }
        } elseif ($request->scope === 'specific_students') {
            $participantIds = $request->student_ids;
        }
        foreach ($participantIds as $studentId) {
            \App\Models\EventParticipant::create([
                'event_id' => $event->event_id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.events.index')->with('success', 'Event created successfully!');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        $event = Event::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['creator', 'section', 'classRoom', 'participants.student', 'consentForms'])
            ->findOrFail($id);
        $participants = $event->participants;
        return view('teacher.events.show', compact('event', 'participants'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        $event = Event::where('created_by', Auth::id())->findOrFail($id);
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        $students = $teacher->getStudents();
        return view('teacher.events.edit', compact('event', 'sections', 'classes', 'students'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'recipient_type' => 'required|in:section,class,students',
            'section_id' => 'required_if:recipient_type,section|nullable|exists:Sections,section_id',
            'class_id' => 'required_if:recipient_type,class|nullable|exists:Classes,class_id',
            'student_ids' => 'required_if:recipient_type,students|array',
            'student_ids.*' => 'exists:Students,student_id',
        ]);

        $teacher = Auth::user()->teacher;
        $event = Event::where('created_by', Auth::id())->findOrFail($id);
        // Verify teacher has access to selected section/class
        if ($request->recipient_type === 'section' && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->with('error', 'Unauthorized access to section.');
        }
        if ($request->recipient_type === 'class' && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->with('error', 'Unauthorized access to class.');
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'cost' => $request->cost ?? 0,
            'scope' => $request->recipient_type,
            'section_id' => $request->section_id,
            'class_id' => $request->class_id,
        ]);

        // Update participants
        $participantIds = [];
        if ($request->recipient_type === 'section') {
            $participantIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
        } elseif ($request->recipient_type === 'class') {
            $class = \App\Models\Classes::with('section')->find($request->class_id);
            if ($class) {
                $participantIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
            }
        } elseif ($request->recipient_type === 'students') {
            $participantIds = $request->student_ids;
        }
        // Remove old participants and add new
        $event->participants()->delete();
        foreach ($participantIds as $studentId) {
            \App\Models\EventParticipant::create([
                'event_id' => $event->event_id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.events.index')->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = Event::where('created_by', Auth::id())->findOrFail($id);
        
        DB::transaction(function () use ($event) {
            $event->participants()->delete();
            $event->delete();
        });

        return redirect()->route('teacher.events.index')->with('success', 'Event deleted successfully!');
    }

    public function participants($id)
    {
        $teacher = Auth::user()->teacher;
        
        $event = Event::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['participants.student', 'section.students', 'classRoom.section.students'])
            ->findOrFail($id);
        
        $students = collect();
        if ($event->section_id) {
            $students = $event->section->students;
        } elseif ($event->class_id) {
            $students = $event->classRoom->getStudents();
        }
        
        $participants = $event->participants->pluck('student_id')->toArray();
        
        return view('teacher.events.participants', compact('event', 'students', 'participants'));
    }

    public function updateParticipants(Request $request, $id)
    {
        $request->validate([
            'participants' => 'array',
            'participants.*' => 'exists:Students,student_id',
        ]);

        $teacher = Auth::user()->teacher;
        $event = Event::where('created_by', Auth::id())->findOrFail($id);
        
        DB::transaction(function () use ($request, $event) {
            // Remove existing participants
            $event->participants()->delete();
            
            // Add new participants
            if ($request->participants) {
                foreach ($request->participants as $studentId) {
                    EventParticipant::create([
                        'event_id' => $event->event_id,
                        'student_id' => $studentId,
                    ]);
                }
            }
        });

        return redirect()->route('teacher.events.participants', $event->event_id)
            ->with('success', 'Event participants updated successfully!');
    }

    public function duplicate($id)
    {
        $teacher = Auth::user()->teacher;
        
        $event = Event::where('created_by', Auth::id())->findOrFail($id);
        
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->created_at = now();
        $newEvent->save();
        
        // Copy participants
        foreach ($event->participants as $participant) {
            EventParticipant::create([
                'event_id' => $newEvent->event_id,
                'student_id' => $participant->student_id,
            ]);
        }
        
        return redirect()->route('teacher.events.edit', $newEvent->event_id)
            ->with('success', 'Event duplicated successfully!');
    }
} 
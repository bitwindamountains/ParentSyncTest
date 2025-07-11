<?php

namespace App\Http\Controllers\Teacher;

use App\Models\ConsentForm;
use App\Models\Event;
use App\Models\Section;
use App\Models\Classes;
use App\Models\ConsentSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class ConsentFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('teacher');
    }

    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $consentForms = ConsentForm::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['event', 'section', 'classRoom', 'signatures'])
            ->latest()
            ->paginate(15);
        
        return view('teacher.consent-forms.index', compact('consentForms'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        $events = Event::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->where('date', '>=', now()->toDateString())
            ->get();
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        $students = $teacher->getStudents();
        
        return view('teacher.consent-forms.create', compact('events', 'sections', 'classes', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_id' => 'nullable|exists:Events,event_id',
            'recipient_type' => 'required|in:section,class,students',
            'section_id' => 'required_if:recipient_type,section|nullable|exists:Sections,section_id',
            'class_id' => 'required_if:recipient_type,class|nullable|exists:Classes,class_id',
            'student_ids' => 'required_if:recipient_type,students|array',
            'student_ids.*' => 'exists:Students,student_id',
            'deadline' => 'required|date|after:today',
        ]);

        $teacher = Auth::user()->teacher;
        // Verify teacher has access to selected section/class
        if ($request->recipient_type === 'section' && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->with('error', 'Unauthorized access to section.');
        }
        if ($request->recipient_type === 'class' && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->with('error', 'Unauthorized access to class.');
        }

        // Verify event access if provided
        if ($request->event_id) {
            $event = Event::where('created_by', Auth::id())
                ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
                ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
                ->find($request->event_id);
            if (!$event) {
                return back()->with('error', 'Unauthorized access to event.');
            }
        }

        $consentForm = ConsentForm::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_id' => $request->event_id,
            'section_id' => $request->section_id,
            'class_id' => $request->class_id,
            'created_by' => Auth::id(),
            'deadline' => $request->deadline,
        ]);

        // Assign recipients
        $recipientIds = [];
        if ($request->recipient_type === 'section') {
            $recipientIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
        } elseif ($request->recipient_type === 'class') {
            $class = \App\Models\Classes::with('section')->find($request->class_id);
            if ($class) {
                $recipientIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
            }
        } elseif ($request->recipient_type === 'students') {
            $recipientIds = $request->student_ids;
        }
        foreach ($recipientIds as $studentId) {
            \App\Models\ConsentFormRecipient::create([
                'form_id' => $consentForm->form_id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.consent-forms.index')->with('success', 'Consent form created successfully!');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        
        $consentForm = ConsentForm::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['event', 'section', 'classRoom', 'signatures.parent', 'signatures.student'])
            ->findOrFail($id);
        
        $students = $consentForm->getStudents();
        $signatures = $consentForm->signatures->keyBy('student_id');
        
        return view('teacher.consent-forms.show', compact('consentForm', 'students', 'signatures'));
    }

    public function edit($id)
    {
        $teacher = Auth::user()->teacher;
        
        $consentForm = ConsentForm::where('created_by', Auth::id())->findOrFail($id);
        $events = Event::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->where('date', '>=', now()->toDateString())
            ->get();
        $sections = $teacher->sections()->with('grade')->get();
        $classes = $teacher->classes()->with(['section', 'subject'])->get();
        
        return view('teacher.consent-forms.edit', compact('consentForm', 'events', 'sections', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_id' => 'nullable|exists:Events,event_id',
            'recipient_type' => 'required|in:section,class,students',
            'section_id' => 'required_if:recipient_type,section|nullable|exists:Sections,section_id',
            'class_id' => 'required_if:recipient_type,class|nullable|exists:Classes,class_id',
            'student_ids' => 'required_if:recipient_type,students|array',
            'student_ids.*' => 'exists:Students,student_id',
            'deadline' => 'required|date',
        ]);

        $teacher = Auth::user()->teacher;
        $consentForm = ConsentForm::where('created_by', Auth::id())->findOrFail($id);
        
        // Verify teacher has access to selected section/class
        if ($request->recipient_type === 'section' && !$teacher->sections->contains('section_id', $request->section_id)) {
            return back()->with('error', 'Unauthorized access to section.');
        }
        
        if ($request->recipient_type === 'class' && !$teacher->classes->contains('class_id', $request->class_id)) {
            return back()->with('error', 'Unauthorized access to class.');
        }

        // Verify event access if provided
        if ($request->event_id) {
            $event = Event::where('created_by', Auth::id())
                ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
                ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
                ->find($request->event_id);
            
            if (!$event) {
                return back()->with('error', 'Unauthorized access to event.');
            }
        }

        $consentForm->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_id' => $request->event_id,
            'section_id' => $request->section_id,
            'class_id' => $request->class_id,
            'deadline' => $request->deadline,
        ]);

        // Update recipients
        $recipientIds = [];
        if ($request->recipient_type === 'section') {
            $recipientIds = \App\Models\Student::where('section_id', $request->section_id)->pluck('student_id')->toArray();
        } elseif ($request->recipient_type === 'class') {
            $class = \App\Models\Classes::with('section')->find($request->class_id);
            if ($class) {
                $recipientIds = \App\Models\Student::where('section_id', $class->section_id)->pluck('student_id')->toArray();
            }
        } elseif ($request->recipient_type === 'students') {
            $recipientIds = $request->student_ids;
        }
        // Remove old recipients and add new
        $consentForm->recipients()->delete();
        foreach ($recipientIds as $studentId) {
            \App\Models\ConsentFormRecipient::create([
                'form_id' => $consentForm->form_id,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->route('teacher.consent-forms.index')->with('success', 'Consent form updated successfully!');
    }

    public function destroy($id)
    {
        $consentForm = ConsentForm::where('created_by', Auth::id())->findOrFail($id);
        
        DB::transaction(function () use ($consentForm) {
            $consentForm->signatures()->delete();
            $consentForm->delete();
        });

        return redirect()->route('teacher.consent-forms.index')->with('success', 'Consent form deleted successfully!');
    }

    public function signatures($id)
    {
        $teacher = Auth::user()->teacher;
        
        $consentForm = ConsentForm::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['signatures.parent', 'signatures.student'])
            ->findOrFail($id);
        
        $students = $consentForm->getStudents();
        $signatures = $consentForm->signatures->keyBy('student_id');
        
        return view('teacher.consent-forms.signatures', compact('consentForm', 'students', 'signatures'));
    }

    public function exportSignatures($id)
    {
        $teacher = Auth::user()->teacher;
        
        $consentForm = ConsentForm::where('created_by', Auth::id())
            ->orWhereIn('section_id', $teacher->sections->pluck('section_id'))
            ->orWhereIn('class_id', $teacher->classes->pluck('class_id'))
            ->with(['signatures.parent', 'signatures.student'])
            ->findOrFail($id);
        
        $students = $consentForm->getStudents();
        $signatures = $consentForm->signatures->keyBy('student_id');
        
        // Generate CSV
        $filename = 'consent_signatures_' . $consentForm->form_id . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students, $signatures) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Parent Name', 'Signed At', 'Status']);
            
            foreach ($students as $student) {
                $signature = $signatures->get($student->student_id);
                fputcsv($file, [
                    $student->full_name,
                    $signature ? $signature->parent->full_name : 'Not signed',
                    $signature ? $signature->signed_at->format('Y-m-d H:i:s') : 'N/A',
                    $signature ? 'Signed' : 'Pending',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function duplicate($id)
    {
        $teacher = Auth::user()->teacher;
        
        $consentForm = ConsentForm::where('created_by', Auth::id())->findOrFail($id);
        
        $newConsentForm = $consentForm->replicate();
        $newConsentForm->title = $consentForm->title . ' (Copy)';
        $newConsentForm->created_at = now();
        $newConsentForm->save();
        
        // Copy recipients
        foreach ($consentForm->recipients as $recipient) {
            \App\Models\ConsentFormRecipient::create([
                'form_id' => $newConsentForm->form_id,
                'student_id' => $recipient->student_id,
            ]);
        }
        
        return redirect()->route('teacher.consent-forms.edit', $newConsentForm->form_id)
            ->with('success', 'Consent form duplicated successfully!');
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\ConsentForm;
use App\Models\ConsentFormRecipient;
use App\Models\AttendanceRecord;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = Section::whereNotNull('teacher_id')->get();
        $classes = Classes::all();
        $students = Student::all();
        $teachers = Teacher::all();

        if ($sections->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No sections or students found. Please run TeacherSeeder and StudentSeeder first.');
            return;
        }

        // Create sample announcements
        $this->createAnnouncements($sections, $students, $teachers);

        // Create sample events
        $this->createEvents($classes, $students, $teachers);

        // Create sample consent forms
        $this->createConsentForms($classes, $students, $teachers);

        // Create sample attendance records
        $this->createAttendanceRecords($sections, $students, $teachers);

        $this->command->info('Sample content created successfully!');
    }

    private function createAnnouncements($sections, $students, $teachers)
    {
        $announcementTitles = [
            'Important Reminder: Parent-Teacher Conference',
            'School Holiday Notice',
            'Sports Day Announcement',
            'Library Week Celebration',
            'Science Fair Registration',
            'Field Trip Permission Slips Due',
            'Exam Schedule Update',
            'School Uniform Policy',
            'After-School Activities',
            'Health Check-up Schedule'
        ];

        foreach (range(1, 15) as $i) {
            $section = $sections->random();
            $teacher = $teachers->random();
            
            $announcement = Announcement::create([
                'title' => $announcementTitles[array_rand($announcementTitles)],
                'content' => 'This is a sample announcement content for testing purposes. Please read carefully and follow the instructions.',
                'created_by' => $teacher->user_id,
                'scope' => 'section',
                'section_id' => $section->section_id,
                'school_id' => $section->grade->school_id,
                'grade_id' => $section->grade_id,
            ]);

            // Create recipients
            $sectionStudents = $students->where('section_id', $section->section_id);
            foreach ($sectionStudents as $student) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->announcement_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }
    }

    private function createEvents($classes, $students, $teachers)
    {
        $eventTitles = [
            'Math Competition',
            'Science Exhibition',
            'Art Workshop',
            'Music Recital',
            'Sports Tournament',
            'Debate Competition',
            'Cooking Class',
            'Photography Workshop',
            'Dance Performance',
            'Theater Play'
        ];

        foreach (range(1, 10) as $i) {
            $class = $classes->random();
            $teacher = $teachers->random();
            $section = $class->section;
            
            $event = Event::create([
                'title' => $eventTitles[array_rand($eventTitles)],
                'description' => 'This is a sample event description for testing purposes.',
                'date' => now()->addDays(rand(1, 30)),
                'time' => '10:00:00',
                'location' => 'Room ' . rand(1, 20),
                'cost' => rand(0, 100),
                'created_by' => $teacher->user_id,
                'scope' => 'class',
                'class_id' => $class->class_id,
                'school_id' => $section->grade->school_id,
                'grade_id' => $section->grade_id,
                'section_id' => $section->section_id,
            ]);

            // Create participants
            $sectionStudents = $students->where('section_id', $section->section_id);
            foreach ($sectionStudents as $student) {
                EventParticipant::create([
                    'event_id' => $event->event_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }
    }

    private function createConsentForms($classes, $students, $teachers)
    {
        $formTitles = [
            'Field Trip Permission Form',
            'Photo Release Form',
            'Medical Information Form',
            'Internet Usage Agreement',
            'Sports Participation Form',
            'After-School Program Consent',
            'Library Card Application',
            'Technology Use Agreement',
            'Swimming Lesson Consent',
            'Off-Campus Activity Permission'
        ];

        foreach (range(1, 8) as $i) {
            $class = $classes->random();
            $teacher = $teachers->random();
            $section = $class->section;
            
            $form = ConsentForm::create([
                'title' => $formTitles[array_rand($formTitles)],
                'description' => 'This is a sample consent form description for testing purposes.',
                'class_id' => $class->class_id,
                'section_id' => $section->section_id,
                'created_by' => $teacher->user_id,
                'deadline' => now()->addDays(rand(7, 21)),
            ]);

            // Create recipients
            $sectionStudents = $students->where('section_id', $section->section_id);
            foreach ($sectionStudents as $student) {
                ConsentFormRecipient::create([
                    'form_id' => $form->form_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }
    }

    private function createAttendanceRecords($sections, $students, $teachers)
    {
        $statuses = ['present', 'absent', 'late', 'excused'];
        
        foreach ($sections as $section) {
            $sectionStudents = $students->where('section_id', $section->section_id);
            $teacher = $teachers->where('teacher_id', $section->teacher_id)->first();
            
            if (!$teacher) continue;

            // Create attendance for the last 10 school days
            for ($day = 1; $day <= 10; $day++) {
                $date = now()->subDays($day);
                
                // Skip weekends
                if ($date->isWeekend()) continue;
                
                foreach ($sectionStudents as $student) {
                    AttendanceRecord::create([
                        'section_id' => $section->section_id,
                        'student_id' => $student->student_id,
                        'date' => $date,
                        'status' => $statuses[array_rand($statuses)],
                        'marked_by' => $teacher->teacher_id,
                    ]);
                }
            }
        }
    }
} 
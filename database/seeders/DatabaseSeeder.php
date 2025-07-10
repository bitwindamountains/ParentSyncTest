<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\ConsentForm;
use App\Models\ConsentFormRecipient;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // School
        $school = \App\Models\School::firstOrCreate([
            'school_name' => 'ParentSync Academy'], [
            'address' => 'Sample Address',
            'contact_no' => '1234567890',
            'email' => 'school@example.com',
        ]);

        // School Years
        $schoolYears = collect(range(2022, 2025))->map(function ($startYear) use ($school) {
            $label = $startYear.'-'.($startYear+1);
            return SchoolYear::firstOrCreate([
                'school_id' => $school->school_id,
                'year_label' => $label
            ]);
        });
        $schoolYear = $schoolYears->last();

        // Subjects
        $subjectList = [
            ['name' => 'Math', 'code' => 'MATH'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'History', 'code' => 'HIST'],
            ['name' => 'PE', 'code' => 'PE'],
            ['name' => 'Music', 'code' => 'MUS'],
            ['name' => 'Art', 'code' => 'ART'],
            ['name' => 'ICT', 'code' => 'ICT'],
            ['name' => 'Filipino', 'code' => 'FIL'],
            ['name' => 'Values', 'code' => 'VAL']
        ];
        $subjects = collect($subjectList)->map(function ($subj) {
            return Subject::firstOrCreate([
                'subject_name' => $subj['name'],
                'subject_code' => $subj['code']
            ], [
                'subject_description' => $subj['name'].' subject.'
            ]);
        });

        // Grades
        $grades = collect(range(1, 12))->map(function ($level) use ($school, $schoolYear) {
            return Grade::firstOrCreate([
                'grade_level' => $level,
                'school_id' => $school->school_id,
                'school_year_id' => $schoolYear->school_year_id,
            ]);
        });

        // Users & Teachers
        $teachers = collect(range(1, 30))->map(function ($i) {
            $user = User::firstOrCreate([
                'username' => 'teacher'.$i,
            ], [
                'password_hash' => Hash::make('password'),
                'role' => 'teacher',
            ]);
            return Teacher::firstOrCreate([
                'teacher_id' => $i,
                'user_id' => $user->user_id,
            ], [
                'first_name' => 'Teacher',
                'last_name' => $i,
                'email' => 'teacher'.$i.'@example.com',
                'contactNo' => '123456789'.$i,
            ]);
        });

        // Sections
        $sections = collect();
        foreach ($grades as $grade) {
            foreach (range(1, 4) as $i) { // 4 sections per grade
                $teacher = $teachers->random();
                $sections->push(Section::firstOrCreate([
                    'section_name' => 'G'.$grade->grade_level.'-S'.$i,
                    'grade_id' => $grade->grade_id,
                    'teacher_id' => $teacher->teacher_id,
                    'school_year_id' => $schoolYear->school_year_id,
                ]));
            }
        }

        // Classes
        $classes = collect();
        foreach ($sections as $section) {
            foreach ($subjects as $subject) {
                $teacher = $teachers->random();
                $classes->push(Classes::firstOrCreate([
                    'section_id' => $section->section_id,
                    'subject_id' => $subject->subject_id,
                    'teacher_id' => $teacher->teacher_id,
                    'school_year_id' => $schoolYear->school_year_id,
                    'start_time' => '08:00:00',
                    'end_time' => '09:00:00',
                ]));
            }
        }

        // Students
        $students = collect();
        $studentId = 1;
        foreach ($sections as $section) {
            foreach (range(1, 35) as $i) { // 35 students per section
                $grade = $grades->firstWhere('grade_id', $section->grade_id);
                $students->push(Student::firstOrCreate([
                    'student_id' => $studentId,
                ], [
                    'first_name' => 'Student',
                    'last_name' => $studentId,
                    'birthdate' => now()->subYears(rand(6, 18))->subDays(rand(0, 365)),
                    'grade_level' => $grade->grade_level,
                    'section_id' => $section->section_id,
                ]));
                $studentId++;
            }
        }

        // Announcements
        foreach (range(1, 60) as $i) {
            $section = $sections->random();
            $announcement = Announcement::create([
                'title' => 'Announcement '.$i,
                'content' => 'Content for announcement '.$i,
                'created_by' => $teachers->random()->user_id,
                'scope' => 'section',
                'section_id' => $section->section_id,
                'school_id' => $school->school_id,
                'grade_id' => $section->grade_id,
            ]);
            foreach ($students->where('section_id', $section->section_id) as $student) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->announcement_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }

        // Events
        foreach (range(1, 40) as $i) {
            $class = $classes->random();
            $event = Event::create([
                'title' => 'Event '.$i,
                'description' => 'Event '.$i.' details.',
                'date' => now()->addDays($i),
                'time' => '10:00:00',
                'location' => 'Room '.rand(1,20),
                'cost' => rand(0,100),
                'created_by' => $teachers->random()->user_id,
                'scope' => 'class',
                'class_id' => $class->class_id,
                'school_id' => $school->school_id,
                'grade_id' => $sections->firstWhere('section_id', $class->section_id)->grade_id,
                'section_id' => $class->section_id,
            ]);
            foreach ($students->where('section_id', $class->section_id) as $student) {
                EventParticipant::create([
                    'event_id' => $event->event_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }

        // Consent Forms
        foreach (range(1, 15) as $i) {
            $class = $classes->random();
            $form = ConsentForm::create([
                'title' => 'Consent Form '.$i,
                'description' => 'Consent Form '.$i.' description.',
                'class_id' => $class->class_id,
                'section_id' => $class->section_id,
                'created_by' => $teachers->random()->user_id,
                'deadline' => now()->addDays(7 + $i),
            ]);
            foreach ($students->where('section_id', $class->section_id) as $student) {
                ConsentFormRecipient::create([
                    'form_id' => $form->form_id,
                    'student_id' => $student->student_id,
                ]);
            }
        }

        // Attendance
        foreach ($students as $student) {
            foreach (range(1, 25) as $day) { // 25 days of attendance
                AttendanceRecord::create([
                    'section_id' => $student->section_id,
                    'student_id' => $student->student_id,
                    'date' => Carbon::now()->subDays($day),
                    'status' => ['present', 'absent', 'late', 'excused'][rand(0, 3)],
                    'marked_by' => $teachers->random()->teacher_id,
                ]);
            }
        }
    }
}

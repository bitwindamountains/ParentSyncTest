<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Subject;

class TeacherSectionSeeder extends Seeder
{
    public function run()
    {
        // Get all teachers
        $teachers = Teacher::all();
        
        // Get all sections that don't have a teacher assigned
        $unassignedSections = Section::whereNull('teacher_id')->get();
        
        // Assign sections to teachers who don't have any
        foreach ($teachers as $teacher) {
            if ($teacher->sections->count() == 0) {
                // Find an unassigned section
                $section = $unassignedSections->shift();
                if ($section) {
                    $section->update(['teacher_id' => $teacher->teacher_id]);
                    
                    // Also create some classes for this teacher in this section
                    $subjects = Subject::take(3)->get();
                    foreach ($subjects as $subject) {
                        Classes::create([
                            'section_id' => $section->section_id,
                            'subject_id' => $subject->subject_id,
                            'teacher_id' => $teacher->teacher_id,
                            'school_year_id' => 1, // Assuming school year 1 exists
                            'start_time' => '08:00:00',
                            'end_time' => '09:00:00',
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Teacher sections assigned successfully!');
    }
} 
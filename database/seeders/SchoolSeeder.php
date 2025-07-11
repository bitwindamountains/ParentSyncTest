<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Section;
use App\Models\SchoolYear;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Create school
        $school = School::firstOrCreate([
            'school_name' => 'ParentSync Academy'
        ], [
            'address' => 'Sample Address, City',
            'contact_no' => '1234567890',
            'email' => 'school@example.com',
        ]);

        // Create school years
        $schoolYears = collect(range(2023, 2025))->map(function ($startYear) use ($school) {
            $label = $startYear.'-'.($startYear+1);
            return SchoolYear::firstOrCreate([
                'school_id' => $school->school_id,
                'year_label' => $label
            ]);
        });
        $currentSchoolYear = $schoolYears->last();

        // Create subjects
        $subjectList = [
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'History', 'code' => 'HIST'],
            ['name' => 'Physical Education', 'code' => 'PE'],
            ['name' => 'Music', 'code' => 'MUS'],
            ['name' => 'Arts', 'code' => 'ART'],
            ['name' => 'ICT', 'code' => 'ICT'],
            ['name' => 'Filipino', 'code' => 'FIL'],
            ['name' => 'Values Education', 'code' => 'VAL']
        ];

        $subjects = collect($subjectList)->map(function ($subj) {
            return Subject::firstOrCreate([
                'subject_name' => $subj['name'],
                'subject_code' => $subj['code']
            ], [
                'subject_description' => $subj['name'].' subject.'
            ]);
        });

        // Create grades (1-6 for elementary)
        $grades = collect(range(1, 6))->map(function ($level) use ($school, $currentSchoolYear) {
            return Grade::firstOrCreate([
                'grade_level' => $level,
                'school_id' => $school->school_id,
                'school_year_id' => $currentSchoolYear->school_year_id,
            ]);
        });

        // Create sections for each grade
        $sectionNames = ['Section A', 'Section B', 'Section C'];
        foreach ($grades as $grade) {
            foreach ($sectionNames as $sectionName) {
                Section::firstOrCreate([
                    'section_name' => $sectionName,
                    'grade_id' => $grade->grade_id,
                    'school_year_id' => $currentSchoolYear->school_year_id,
                ], [
                    'teacher_id' => null, // Will be assigned later
                ]);
            }
        }

        $this->command->info('School, grades, sections, and subjects created successfully!');
    }
} 
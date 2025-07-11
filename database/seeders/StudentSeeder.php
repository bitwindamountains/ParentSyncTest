<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Section;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all sections that have teachers assigned
        $sections = Section::whereNotNull('teacher_id')->get();
        
        if ($sections->isEmpty()) {
            $this->command->warn('No sections with teachers found. Please run TeacherSeeder first.');
            return;
        }

        $studentId = 1001;
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'James', 'Emma', 'William', 'Olivia'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];

        foreach ($sections as $section) {
            // Create 20 students per section
            for ($i = 1; $i <= 20; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                
                Student::firstOrCreate([
                    'student_id' => $studentId
                ], [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birthdate' => now()->subYears(rand(6, 12))->subDays(rand(0, 365)),
                    'grade_level' => $section->grade->grade_level,
                    'section_id' => $section->section_id,
                ]);
                
                $studentId++;
            }
        }

        $this->command->info('Students created and assigned to sections successfully!');
        $this->command->info('Created ' . ($studentId - 1001) . ' students across ' . $sections->count() . ' sections.');
    }
} 
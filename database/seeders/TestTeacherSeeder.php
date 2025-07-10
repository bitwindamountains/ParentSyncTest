<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class TestTeacherSeeder extends Seeder
{
    public function run()
    {
        // Create a test user
        $user = User::create([
            'username' => 'teacher1',
            'password_hash' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        // Create a test teacher
        $teacher = Teacher::create([
            'teacher_id' => 'T' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'user_id' => $user->user_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@school.com',
            'contactNo' => '09123456789',
        ]);

        // Find a section that doesn't have a teacher assigned
        $section = Section::whereNull('teacher_id')->first();
        
        if ($section) {
            // Assign the section to the teacher
            $section->update(['teacher_id' => $teacher->teacher_id]);
            
            // Create classes for this teacher in this section
            $subjects = Subject::take(3)->get();
            foreach ($subjects as $subject) {
                Classes::create([
                    'section_id' => $section->section_id,
                    'subject_id' => $subject->subject_id,
                    'teacher_id' => $teacher->teacher_id,
                    'school_year_id' => 1,
                    'start_time' => '08:00:00',
                    'end_time' => '09:00:00',
                ]);
            }
            
            $this->command->info('Test teacher created successfully!');
            $this->command->info('Username: teacher1');
            $this->command->info('Password: password123');
            $this->command->info('Assigned to section: ' . $section->section_name);
        } else {
            $this->command->error('No unassigned sections found!');
        }
    }
} 
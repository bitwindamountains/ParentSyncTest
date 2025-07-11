<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\SchoolYear;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Get current school year
        $schoolYear = SchoolYear::latest()->first();
        
        // Create sample teachers
        $teachers = [
            [
                'teacher_id' => 1001,
                'username' => 'teacher001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@school.com',
                'contactNo' => '09123456789',
                'password' => 'password123'
            ],
            [
                'teacher_id' => 1002,
                'username' => 'teacher002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@school.com',
                'contactNo' => '09123456790',
                'password' => 'password123'
            ],
            [
                'teacher_id' => 1003,
                'username' => 'teacher003',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@school.com',
                'contactNo' => '09123456791',
                'password' => 'password123'
            ],
            [
                'teacher_id' => 1004,
                'username' => 'teacher004',
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@school.com',
                'contactNo' => '09123456792',
                'password' => 'password123'
            ],
            [
                'teacher_id' => 1005,
                'username' => 'teacher005',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@school.com',
                'contactNo' => '09123456793',
                'password' => 'password123'
            ],
        ];

        $createdTeachers = collect();

        foreach ($teachers as $teacherData) {
            // Create user account
            $user = User::firstOrCreate([
                'username' => $teacherData['username']
            ], [
                'password_hash' => Hash::make($teacherData['password']),
                'role' => 'teacher',
            ]);

            // Create teacher profile
            $teacher = Teacher::firstOrCreate([
                'teacher_id' => $teacherData['teacher_id']
            ], [
                'user_id' => $user->user_id,
                'first_name' => $teacherData['first_name'],
                'last_name' => $teacherData['last_name'],
                'email' => $teacherData['email'],
                'contactNo' => $teacherData['contactNo'],
            ]);

            $createdTeachers->push($teacher);
        }

        // Assign teachers to sections
        $unassignedSections = Section::whereNull('teacher_id')->get();
        $subjects = Subject::all();

        foreach ($createdTeachers as $index => $teacher) {
            if ($index < $unassignedSections->count()) {
                $section = $unassignedSections[$index];
                
                // Assign section to teacher
                $section->update(['teacher_id' => $teacher->teacher_id]);
                
                // Create classes for this teacher in this section
                $sectionSubjects = $subjects->take(5); // Assign 5 subjects per teacher
                foreach ($sectionSubjects as $subject) {
                    Classes::firstOrCreate([
                        'section_id' => $section->section_id,
                        'subject_id' => $subject->subject_id,
                        'teacher_id' => $teacher->teacher_id,
                        'school_year_id' => $schoolYear->school_year_id,
                    ], [
                        'start_time' => '08:00:00',
                        'end_time' => '09:00:00',
                    ]);
                }
            }
        }

        $this->command->info('Teachers created and assigned to sections successfully!');
        $this->command->info('Sample teacher credentials:');
        $this->command->info('Username: teacher001, Password: password123');
    }
} 
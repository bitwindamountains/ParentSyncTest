<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Section;
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
        $this->call([
            SchoolSeeder::class,
            AdminSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            ContentSeeder::class,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';

    protected $fillable = [
        'section_name',
        'grade_id',
        'teacher_id',
        'school_year_id',
    ];

    /**
     * Get the grade that owns the section.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'grade_id');
    }

    /**
     * Get the teacher assigned to this section.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Get the students in this section.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'section_id', 'section_id');
    }

    /**
     * Get the classes in this section.
     */
    public function classes()
    {
        return $this->hasMany(Classes::class, 'section_id', 'section_id');
    }

    /**
     * Get the announcements for this section.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'section_id', 'section_id');
    }

    /**
     * Get the events for this section.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'section_id', 'section_id');
    }

    /**
     * Get the consent forms for this section.
     */
    public function consentForms()
    {
        return $this->hasMany(ConsentForm::class, 'section_id', 'section_id');
    }

    /**
     * Get the attendance records for this section.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'section_id', 'section_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'school_year_id');
    }
} 
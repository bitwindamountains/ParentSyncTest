<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';

    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
        'school_year_id',
        'start_time',
        'end_time',
    ];

    /**
     * Get the section that owns the class.
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * Get the subject that owns the class.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Get the teacher that owns the class.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Get the announcements for this class.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'class_id', 'class_id');
    }

    /**
     * Get the events for this class.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'class_id', 'class_id');
    }

    /**
     * Get the consent forms for this class.
     */
    public function consentForms()
    {
        return $this->hasMany(ConsentForm::class, 'class_id', 'class_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'school_year_id');
    }

    /**
     * Get all students in the section for this class.
     */
    public function getStudents()
    {
        return $this->section->students;
    }

    /**
     * Get the schedules for this class.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id', 'class_id');
    }

    /**
     * Get the display name for this class.
     */
    public function getClassDisplayNameAttribute()
    {
        return $this->subject->subject_name . ' - ' . $this->section->section_name;
    }

    /**
     * Get the class name (alias for display name).
     */
    public function getClassNameAttribute()
    {
        return $this->class_display_name;
    }
} 
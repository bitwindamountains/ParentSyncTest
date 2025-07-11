<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'birthdate',
        'grade_level',
        'section_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Get the section that owns the student.
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * Get the parents linked to this student.
     */
    public function parents()
    {
        return $this->belongsToMany(ParentUser::class, 'parent_student_link', 'student_id', 'parent_id')
                    ->withPivot('verified', 'linked_at')
                    ->withTimestamps();
    }

    /**
     * Get the attendance records for this student.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id', 'student_id');
    }

    /**
     * Get the consent signatures for this student.
     */
    public function consentSignatures()
    {
        return $this->hasMany(ConsentSignature::class, 'student_id', 'student_id');
    }

    /**
     * Get the announcement recipients for this student.
     */
    public function announcementRecipients()
    {
        return $this->hasMany(AnnouncementRecipient::class, 'student_id', 'student_id');
    }

    /**
     * Get the event participants for this student.
     */
    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class, 'student_id', 'student_id');
    }

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
} 
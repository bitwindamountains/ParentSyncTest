<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'teacher_id';
    public $incrementing = false;

    protected $fillable = [
        'teacher_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'contactNo',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the sections assigned to this teacher.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'teacher_id', 'teacher_id');
    }
    
    /**
     * Get the classes taught by this teacher.
     */
    public function classes()
    {
        return $this->hasMany(Classes::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Get the attendance records marked by this teacher.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'marked_by', 'teacher_id');
    }

    /**
     * Get the full name of the teacher.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get all students assigned to this teacher (via sections).
     */
    public function getStudents()
    {
        return Student::whereHas('section', function ($query) {
            $query->where('teacher_id', $this->teacher_id);
        })->get();
    }

    public function getClassesWithStudents()
    {
        return $this->classes()->with(['section.students', 'subject'])->get();
    }
} 
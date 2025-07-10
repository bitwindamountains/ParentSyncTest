<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $primaryKey = 'grade_id';

    protected $fillable = [
        'grade_level',
        'school_id',
        'school_year_id',
    ];

    /**
     * Get the school that owns the grade.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }

    /**
     * Get the sections for this grade.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'grade_id', 'grade_id');
    }

    /**
     * Get the announcements for this grade.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'grade_id', 'grade_id');
    }

    /**
     * Get the events for this grade.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'grade_id', 'grade_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'school_year_id');
    }
} 
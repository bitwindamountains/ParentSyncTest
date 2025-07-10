<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'student_id',
    ];

    /**
     * Get the announcement that owns the recipient.
     */
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id', 'announcement_id');
    }

    /**
     * Get the student that owns the recipient.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
} 
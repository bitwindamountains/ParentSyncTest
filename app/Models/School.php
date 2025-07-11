<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $primaryKey = 'school_id';

    protected $fillable = [
        'school_name',
        'address',
        'contact_no',
        'email',
    ];

    /**
     * Get the grades for this school.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'school_id', 'school_id');
    }

    /**
     * Get the announcements for this school.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'school_id', 'school_id');
    }

    /**
     * Get the events for this school.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'school_id', 'school_id');
    }
} 
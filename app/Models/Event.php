<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'cost',
        'created_by',
        'scope',
        'school_id',
        'grade_id',
        'section_id',
        'class_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the user that created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the school that owns the event.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }

    /**
     * Get the grade that owns the event.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'grade_id');
    }

    /**
     * Get the section that owns the event.
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * Get the class that owns the event.
     */
    public function classRoom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    /**
     * Get the participants for this event.
     */
    public function participants()
    {
        return $this->hasMany(EventParticipant::class, 'event_id', 'event_id');
    }

    /**
     * Get the consent forms for this event.
     */
    public function consentForms()
    {
        return $this->hasMany(ConsentForm::class, 'event_id', 'event_id');
    }

    /**
     * Get the display color for the scope.
     */
    public function getScopeColorAttribute()
    {
        switch ($this->scope) {
            case 'section': return 'success';
            case 'class': return 'info';
            case 'individual': return 'warning';
            default: return 'secondary';
        }
    }

    /**
     * Get the display label for the scope.
     */
    public function getScopeDisplayAttribute()
    {
        switch ($this->scope) {
            case 'section': return 'Section';
            case 'class': return 'Class';
            case 'individual': return 'Individual';
            default: return ucfirst($this->scope ?? 'General');
        }
    }

    /**
     * Get the urgent flag (default false if not present).
     */
    public function getIsUrgentAttribute()
    {
        return $this->attributes['is_urgent'] ?? false;
    }

    /**
     * Get the attachment path (default null if not present).
     */
    public function getAttachmentPathAttribute()
    {
        return $this->attributes['attachment_path'] ?? null;
    }
} 
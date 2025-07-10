<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentForm extends Model
{
    use HasFactory;

    protected $primaryKey = 'form_id';

    protected $fillable = [
        'title',
        'description',
        'event_id',
        'section_id',
        'class_id',
        'created_by',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Get the user that created the consent form.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the event that owns the consent form.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * Get the section that owns the consent form.
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * Get the class that owns the consent form.
     */
    public function classRoom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    /**
     * Get the signatures for this consent form.
     */
    public function signatures()
    {
        return $this->hasMany(ConsentSignature::class, 'form_id', 'form_id');
    }

    /**
     * Get the recipients for this consent form.
     */
    public function recipients()
    {
        return $this->hasMany(ConsentFormRecipient::class, 'form_id', 'form_id');
    }

    /**
     * Get all students for this consent form based on scope.
     */
    public function getStudents()
    {
        if ($this->section_id) {
            return $this->section->students;
        } elseif ($this->class_id) {
            return $this->classRoom->getStudents();
        } else {
            return collect();
        }
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
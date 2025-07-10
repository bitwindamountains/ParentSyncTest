<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentSignature extends Model
{
    use HasFactory;

    protected $primaryKey = 'signature_id';

    protected $fillable = [
        'form_id',
        'parent_id',
        'student_id',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Get the consent form that owns the signature.
     */
    public function consentForm()
    {
        return $this->belongsTo(ConsentForm::class, 'form_id', 'form_id');
    }

    /**
     * Get the parent that owns the signature.
     */
    public function parent()
    {
        return $this->belongsTo(Parent::class, 'parent_id', 'parent_id');
    }

    /**
     * Get the student that owns the signature.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
} 
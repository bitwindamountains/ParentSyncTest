<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentUser extends Model
{
    use HasFactory;

    protected $primaryKey = 'parent_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'contactNo',
    ];

    /**
     * Get the user that owns the parent.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the students linked to this parent.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student_link', 'parent_id', 'student_id')
                    ->withPivot('verified', 'linked_at')
                    ->withTimestamps();
    }

    /**
     * Get the consent signatures by this parent.
     */
    public function consentSignatures()
    {
        return $this->hasMany(ConsentSignature::class, 'parent_id', 'parent_id');
    }

    /**
     * Get the full name of the parent.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
} 
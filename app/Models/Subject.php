<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_name',
        'subject_code',
        'subject_description',
    ];

    /**
     * Get the classes for this subject.
     */
    public function classes()
    {
        return $this->hasMany(Classes::class, 'subject_id', 'subject_id');
    }
} 
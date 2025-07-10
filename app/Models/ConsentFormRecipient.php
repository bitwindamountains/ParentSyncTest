<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentFormRecipient extends Model
{
    use HasFactory;

    protected $table = 'ConsentFormRecipients';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'form_id',
        'student_id',
    ];

    public function consentForm()
    {
        return $this->belongsTo(ConsentForm::class, 'form_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
} 
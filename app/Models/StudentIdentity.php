<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentIdentity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lrn', 
        'fullname', 
        'dob', 
        'level', 
        'adviser_id', 
        'is_active',
        'profile_picture'
    ];

    // FIX: Prevents SQL errors during student status checks
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function adviser()
    {
        return $this->belongsTo(TeacherIdentity::class, 'adviser_id');
    }
}

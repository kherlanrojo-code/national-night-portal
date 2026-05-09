<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'lrn', 
        'subject', 
        'subject_code',
        'grade', 
        'semester', 
        'quarter',
        'is_submitted_to_admin',
        'is_published'
    ];

    // FIX: Tells PostgreSQL these are Booleans, not Integers
    protected $casts = [
        'is_submitted_to_admin' => 'boolean',
        'is_published' => 'boolean',
    ];
}

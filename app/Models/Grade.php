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

    protected $casts = [
        'is_submitted_to_admin' => 'boolean',
        'is_published' => 'boolean',
    ];

    // Add this helper to prevent "N/A"
    public function getDisplayCodeAttribute()
    {
        return $this->subject_code ?? 'No Code';
    }
}

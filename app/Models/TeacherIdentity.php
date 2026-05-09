<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherIdentity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'is_active',
    ];

    // This must be INSIDE the class braces
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students()
    {
        // Your current code uses adviser_id to link teachers to students
        return $this->hasMany(StudentIdentity::class, 'adviser_id');
    }

            
            public function grades()
        {
            return $this->hasManyThrough(
                \App\Models\Grade::class,
                \App\Models\StudentIdentity::class,
                'adviser_id', // Links to teacher_identities.id
                'lrn',        // Links to grades.lrn
                'id',         // Local key on teacher_identities
                'lrn'         // Local key on student_identities
            );
        }
    public function getFullnameAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return "{$this->first_name} " . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    } 
} // <--- Make sure this final brace is at the very end of the file

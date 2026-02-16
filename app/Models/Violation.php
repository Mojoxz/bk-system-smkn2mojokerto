<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_type_id',
        'admin_id',
        'description',
        'points',
        'photo_evidence',
        'signature',
        'violation_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'violation_date' => 'datetime',
        'points' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function violationType()
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function booted()
    {
        static::saved(function ($violation) {
            if ($violation->status === 'approved') {
                $violation->student->updateTotalPoints();
            }
        });

        static::deleted(function ($violation) {
            $violation->student->updateTotalPoints();
        });
    }
}

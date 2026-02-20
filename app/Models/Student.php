<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nisn',
        'name',
        'classroom_id', 
        'absen',
        'username',
        'password',
        'phone',
        'address',
        'total_points',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'     => 'hashed',
        'is_active'    => 'boolean',
        'total_points' => 'integer',
    ];


    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }


    public function updateTotalPoints(): void
    {
        $this->total_points = $this->violations()
            ->where('status', 'approved')
            ->sum('points');
        $this->save();
    }
}

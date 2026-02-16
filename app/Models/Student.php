<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nisn',
        'name',
        'class',
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
        'password' => 'hashed',
        'is_active' => 'boolean',
        'total_points' => 'integer',
    ];

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function updateTotalPoints()
    {
        $this->total_points = $this->violations()
            ->where('status', 'approved')
            ->sum('points');
        $this->save();
    }
}

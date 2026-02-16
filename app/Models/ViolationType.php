<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_category_id',
        'name',
        'description',
        'points',
        'is_custom',
        'is_active',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
        'points' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

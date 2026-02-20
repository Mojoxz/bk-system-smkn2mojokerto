<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'major_id',
        'name',
        'grade',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Label lengkap untuk dropdown: "XI RPL 1 (RPL)"
     */
    public function getFullLabelAttribute(): string
    {
        return $this->name . ' (' . ($this->major->code ?? '-') . ')';
    }
}

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
        'photo',
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

    // ── Relasi ──────────────────────────────────────────────────

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    // ── Helper ──────────────────────────────────────────────────

    /**
     * URL foto profil. Mengembalikan null jika belum ada foto.
     * Pakai asset() agar kompatibel semua versi Laravel dan tidak
     * memicu false-positive IntelliSense di VS Code.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }

    /**
     * Inisial nama (maks. 2 kata) untuk avatar fallback.
     */
    public function getInitialsAttribute(): string
    {
        $words    = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: '?';
    }

    /**
     * Perbarui total poin dari pelanggaran yang disetujui.
     */
    public function updateTotalPoints(): void
    {
        $this->total_points = $this->violations()
            ->where('status', 'approved')
            ->sum('points');
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_event',
        'jumlah_peserta',
        'waktu_pelaksanaan',
        'tempat_pelaksanaan',
        'created_by',
    ];

    protected $casts = [
        'waktu_pelaksanaan' => 'date',
        'jumlah_peserta' => 'integer',
    ];

    // Relation: Event dibuat oleh user
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation: Event memiliki banyak kehadiran
    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    // Helper: Total yang hadir
    public function getTotalHadirAttribute(): int
    {
        return $this->attendances()->count();
    }

    // Helper: Persentase kehadiran
    public function getPersenHadirAttribute(): float
    {
        if ($this->jumlah_peserta == 0) return 0;
        return round(($this->total_hadir / $this->jumlah_peserta) * 100, 2);
    }

    // Helper: Check if user sudah hadir
    public function isUserAttended(int $userId): bool
    {
        return $this->attendances()->where('user_id', $userId)->exists();
    }

    // Helper: Sisa kuota peserta
    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->jumlah_peserta - $this->total_hadir);
    }

    // Helper: Status event (upcoming, ongoing, finished)
    public function getStatusEventAttribute(): string
    {
        $today = now()->startOfDay();
        $eventDate = $this->waktu_pelaksanaan->startOfDay();

        if ($eventDate->isFuture()) {
            return 'upcoming';
        } elseif ($eventDate->isToday()) {
            return 'ongoing';
        } else {
            return 'finished';
        }
    }
}
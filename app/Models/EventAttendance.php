<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'nik',
        'name',
        'username',
        'role',
        'bidang',
        'waktu_hadir',
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    // Relation: Kehadiran untuk event tertentu
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relation: Kehadiran oleh user tertentu
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper: Format waktu hadir
    public function getWaktuHadirFormattedAttribute(): string
    {
        return $this->waktu_hadir->format('d M Y, H:i:s');
    }
}
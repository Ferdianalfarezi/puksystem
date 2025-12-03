<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nik',
        'username',
        'password',
        'role_id',
        'bidang_id',
        'departemen',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ✅ NEW: Relations untuk Events
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function eventAttendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    // ✅ NEW: Helper untuk generate QR Code data
    public function getQrCodeDataAttribute(): string
    {
        return $this->nik ?? ''; // NIK akan di-encode ke QR Code
    }

    // ✅ NEW: Check if user attended specific event
    public function hasAttendedEvent(int $eventId): bool
    {
        return $this->eventAttendances()->where('event_id', $eventId)->exists();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Koorlap extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'user_id',
    ];

    /**
     * Relasi ke User (1 Koorlap = 1 User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Users yang diawasi (1 Koorlap punya banyak User)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'koorlap_id');
    }

    /**
     * Get role dari user terkait
     */
    public function getRoleAttribute()
    {
        return $this->user?->role;
    }

    /**
     * Get bidang dari user terkait
     */
    public function getBidangAttribute()
    {
        return $this->user?->bidang;
    }

    /**
     * Get user count
     */
    public function getUserCountAttribute(): int
    {
        return $this->users()->count();
    }
}
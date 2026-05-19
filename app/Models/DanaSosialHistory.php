<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanaSosialHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'dana_sosial_id',
        'koorlap_id',
        'user_id',
        'jenis',
        'nominal',
        'evident',
        'status',
        'approved_by',
        'approved_at',
        'catatan_approval',
        'verified_by',
        'verified_at',
        'completed_at',
        'original_created_at',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'approved_at' => 'datetime',
        'verified_at' => 'datetime',
        'completed_at' => 'datetime',
        'original_created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function koorlap(): BelongsTo
    {
        return $this->belongsTo(Koorlap::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForKoorlap($query, $koorlapId)
    {
        return $query->where('koorlap_id', $koorlapId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDiserahkan($query)
    {
        return $query->where('status', 'diserahkan');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // ========================================
    // ATTRIBUTE ACCESSORS
    // ========================================

    public function getJenisLabelAttribute(): string
    {
        return DanaSosial::JENIS[$this->jenis] ?? ucfirst(str_replace('_', ' ', $this->jenis));
    }

    public function getStatusLabelAttribute(): string
    {
        return DanaSosial::STATUS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'menunggu_persetujuan_bidang_sosial' => 'bg-yellow-100 text-yellow-800',
            'disetujui' => 'bg-blue-100 text-blue-800',
            'ditolak' => 'bg-red-100 text-red-800',
            'diserahkan' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getJenisBadgeClassAttribute(): string
    {
        return match($this->jenis) {
            'rawat_inap' => 'bg-blue-100 text-blue-800',
            'duka_cita' => 'bg-purple-100 text-purple-800',
            'banjir' => 'bg-cyan-100 text-cyan-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'bidang_id',
        'nama',
        'anggaran',
        'tahun',
        'status',
        'submitted_at',
        'submitted_by',
        'reviewed_by_bendahara',
        'reviewed_at_bendahara',
        'catatan_bendahara',
        'reviewed_by_ketua',
        'reviewed_at_ketua',
        'catatan_ketua',
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at_bendahara' => 'datetime',
        'reviewed_at_ketua' => 'datetime',
    ];

    // Relationships
    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedByBendahara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_bendahara');
    }

    public function reviewedByKetua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_ketua');
    }

    // Scopes
    public function scopeForBidang($query, $bidangId)
    {
        return $query->where('bidang_id', $bidangId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft';
    }

    public function isWaitingBendahara(): bool
    {
        return $this->status === 'menunggu_konfirmasi_bendahara';
    }

    public function isWaitingKetua(): bool
    {
        return $this->status === 'menunggu_approval_ketua';
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['ditolak_bendahara', 'ditolak_ketua']);
    }

    // Status badge color helper untuk view
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'badge-secondary',
            'menunggu_konfirmasi_bendahara' => 'badge-warning',
            'menunggu_approval_ketua' => 'badge-info',
            'ditolak_bendahara', 'ditolak_ketua' => 'badge-danger',
            'disetujui' => 'badge-success',
            default => 'badge-secondary',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispensasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_budget_id',
        'bidang_id',
        'user_ids',
        'keterangan',
        'status',
        'submitted_by',
        'submitted_at',
        'reviewed_by_sekretaris',
        'reviewed_at_sekretaris',
        'catatan_sekretaris',
        'reviewed_by_ketua',
        'reviewed_at_ketua',
        'catatan_ketua',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at_sekretaris' => 'datetime',
        'reviewed_at_ketua' => 'datetime',
    ];

    // Relationships
    public function pengajuanBudget(): BelongsTo
    {
        return $this->belongsTo(PengajuanBudget::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBySekretaris(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_sekretaris');
    }

    public function reviewedByKetua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_ketua');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DispensasiHistory::class);
    }

    // Helper untuk get user objects
    public function getUsers()
    {
        return User::whereIn('id', $this->user_ids ?? [])->get();
    }

    // Status checkers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft';
    }

    public function isWaitingSekretaris(): bool
    {
        return $this->status === 'menunggu_approval_sekretaris';
    }

    public function isWaitingKetua(): bool
    {
        return $this->status === 'menunggu_approval_ketua';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['ditolak_sekretaris', 'ditolak_ketua']);
    }

    // Badge helpers
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'menunggu_approval_sekretaris' => 'bg-yellow-100 text-yellow-800',
            'menunggu_approval_ketua' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'ditolak_sekretaris', 'ditolak_ketua' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Dalam Proses',
            'menunggu_approval_sekretaris' => 'Menunggu Sekretaris',
            'menunggu_approval_ketua' => 'Menunggu Ketua',
            'approved' => 'Disetujui',
            'ditolak_sekretaris' => 'Ditolak Sekretaris',
            'ditolak_ketua' => 'Ditolak Ketua',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    
}
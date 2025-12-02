<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'bidang_id',
        'nama',
        'anggaran',
        'jenis_pengeluaran',
        'tahun',
        'tanggal',
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
        'tanggal' => 'date',
    ];

    // ✅ Konstanta untuk jenis pengeluaran (SAMA dengan ProgramKerja)
    public const JENIS_PENGELUARAN = [
        'Kesekretariatan',
        'Perjalanan Dinas',
        'Aksi',
        'Dana Sosial',
        'Dansos Duka',
        'Dansos Banjir',
        'Pendidikan',
        'Rapat',
        'COS DPP',
        'Iuaran FKJ',
        'Dansos Ekternal',
        'Iuran GM',
        'Rapat GM'
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

    public function pencairan(): HasOne
    {
        return $this->hasOne(PencairanBudget::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PengajuanBudgetHistory::class);
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

    public function scopeByJenisPengeluaran($query, $jenis)
    {
        return $query->where('jenis_pengeluaran', $jenis);
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

    public function isMenungguPencairan(): bool
    {
        return $this->status === 'menunggu_pencairan';
    }

    public function isDicairkan(): bool
    {
        return $this->status === 'dicairkan';
    }

    public function canBeCairkan(): bool
    {
        return $this->status === 'menunggu_pencairan';
    }

    public function isApproved(): bool
    {
        return $this->status === 'menunggu_pencairan';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['ditolak_bendahara', 'ditolak_ketua']);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'menunggu_konfirmasi_bendahara' => 'bg-yellow-100 text-yellow-800',
            'menunggu_approval_ketua' => 'bg-blue-100 text-blue-800',
            'menunggu_pencairan' => 'bg-purple-100 text-purple-800',
            'dicairkan' => 'bg-green-100 text-green-800',
            'ditolak_bendahara', 'ditolak_ketua' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'menunggu_konfirmasi_bendahara' => 'Menunggu Bendahara',
            'menunggu_approval_ketua' => 'Menunggu Ketua',
            'menunggu_pencairan' => 'Menunggu Pencairan',
            'dicairkan' => 'Dicairkan',
            'ditolak_bendahara' => 'Ditolak Bendahara',
            'ditolak_ketua' => 'Ditolak Ketua',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getJenisPengeluaranBadgeClass(): string
    {
        return match($this->jenis_pengeluaran) {
            'Kesekretariatan' => 'bg-blue-100 text-blue-800',
            'Perjalanan Dinas' => 'bg-purple-100 text-purple-800',
            'Aksi' => 'bg-green-100 text-green-800',
            'Dana Sosial', 'Dansos Duka', 'Dansos Banjir', 'Dansos Ekternal' => 'bg-pink-100 text-pink-800',
            'Pendidikan' => 'bg-yellow-100 text-yellow-800',
            'Rapat', 'Rapat GM' => 'bg-gray-100 text-gray-800',
            'COS DPP' => 'bg-indigo-100 text-indigo-800',
            'Iuaran FKJ', 'Iuran GM' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
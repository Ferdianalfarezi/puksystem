<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanHutang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hutang';

    protected $fillable = [
        'user_id',
        'bidang_id',
        'nama',
        'jumlah',
        'sisa_hutang',
        'keperluan',
        'tanggal',
        'tahun',
        'status',
        'submitted_by',
        'submitted_at',
        'reviewed_by_bendahara',
        'reviewed_at_bendahara',
        'catatan_bendahara',
        'reviewed_by_ketua',
        'reviewed_at_ketua',
        'catatan_ketua',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'sisa_hutang' => 'decimal:2',
        'tanggal' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at_bendahara' => 'datetime',
        'reviewed_at_ketua' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranHutang::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PengajuanHutangHistory::class);
    }

    // Status Checks
    public function isDraft(): bool
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

    public function isDicairkan(): bool
    {
        return $this->status === 'dicairkan';
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft();
    }

    public function canBePaid(): bool
    {
        return $this->isDicairkan() && $this->sisa_hutang > 0;
    }

    // Helpers
    public function getPersenLunasAttribute(): float
    {
        if ($this->jumlah == 0) return 0;
        $terbayar = $this->jumlah - $this->sisa_hutang;
        return ($terbayar / $this->jumlah) * 100;
    }

    public function getTotalTerbayarAttribute(): float
    {
        return $this->jumlah - $this->sisa_hutang;
    }
}
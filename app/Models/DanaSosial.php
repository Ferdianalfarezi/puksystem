<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DanaSosial extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'approved_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ========================================
    // CONSTANTS
    // ========================================
    
    public const JENIS = [
        'rawat_inap' => 'Rawat Inap',
        'duka_cita' => 'Duka Cita',
        'banjir' => 'Banjir',
    ];

    public const NOMINAL_FIXED = [
        'rawat_inap' => 300000,
        'banjir' => 200000,
    ];

    public const STATUS = [
        'menunggu_persetujuan_bidang_sosial' => 'Menunggu Persetujuan Bidang Sosial',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'diserahkan' => 'Diserahkan',
    ];

    public const BIDANG_SOSIAL_ID = 4;

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

    public function scopeMenungguApproval($query)
    {
        return $query->where('status', 'menunggu_persetujuan_bidang_sosial');
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'disetujui');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    public static function getNominalByJenis(string $jenis): ?float
    {
        return self::NOMINAL_FIXED[$jenis] ?? null;
    }

    public function isMenungguApproval(): bool
    {
        return $this->status === 'menunggu_persetujuan_bidang_sosial';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isDiserahkan(): bool
    {
        return $this->status === 'diserahkan';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'menunggu_persetujuan_bidang_sosial';
    }

    public function canBeVerified(): bool
    {
        return $this->status === 'disetujui';
    }

    // ========================================
    // ATTRIBUTE ACCESSORS
    // ========================================

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? ucfirst(str_replace('_', ' ', $this->jenis));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
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

    // ========================================
    // MOVE TO HISTORY
    // ========================================

    public function moveToHistory(): DanaSosialHistory
    {
        return DB::transaction(function () {
            $history = DanaSosialHistory::create([
                'dana_sosial_id' => $this->id,
                'koorlap_id' => $this->koorlap_id,
                'user_id' => $this->user_id,
                'jenis' => $this->jenis,
                'nominal' => $this->nominal,
                'evident' => $this->evident,
                'status' => $this->status,
                'approved_by' => $this->approved_by,
                'approved_at' => $this->approved_at,
                'catatan_approval' => $this->catatan_approval,
                'verified_by' => $this->verified_by,
                'verified_at' => $this->verified_at,
                'completed_at' => now(),
                'original_created_at' => $this->created_at,
            ]);

            $this->delete();

            return $history;
        });
    }
}
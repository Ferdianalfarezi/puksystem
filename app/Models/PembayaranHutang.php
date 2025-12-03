<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranHutang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_hutang';

    protected $fillable = [
        'pengajuan_hutang_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'nomor_referensi',
        'catatan',
        'dibayar_oleh',
        'history_kas_id',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    // Relations
    public function pengajuanHutang(): BelongsTo
    {
        return $this->belongsTo(PengajuanHutang::class);
    }

    public function dibayarOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibayar_oleh');
    }

    // ✅ FIX: Relasi ke HistoryKas (bukan KasHistory)
    public function historyKas(): BelongsTo
    {
        return $this->belongsTo(HistoryKas::class, 'history_kas_id');
    }

    // Helper
    public function getMetodePembayaranLabelAttribute(): string
    {
        return match($this->metode_pembayaran) {
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
            default => $this->metode_pembayaran,
        };
    }
}
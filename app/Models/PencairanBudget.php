<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencairanBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_budget_id',
        'jumlah_dicairkan',
        'tanggal_pencairan',
        'metode_pencairan',
        'nomor_referensi',
        'catatan',
        'dicairkan_oleh',
    ];

    protected $casts = [
        'jumlah_dicairkan' => 'decimal:2',
        'tanggal_pencairan' => 'datetime',
    ];

    public function pengajuanBudget(): BelongsTo
    {
        return $this->belongsTo(PengajuanBudget::class);
    }

    public function dicairkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    public function getMetodePencairanLabelAttribute(): string
    {
        return match($this->metode_pencairan) {
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
            default => ucfirst(str_replace('_', ' ', $this->metode_pencairan)),
        };
    }
}
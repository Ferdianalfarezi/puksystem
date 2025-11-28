<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pencairan extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_kerja_id',
        'tanggal_program',
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
        'tanggal_program' => 'date',
    ];

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class);
    }

    public function dicairkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    public function getFormattedJumlahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_dicairkan, 0, ',', '.');
    }

    public function getMetodePencairanLabelAttribute(): string
    {
        return match($this->metode_pencairan) {
            'transfer' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            default => ucfirst($this->metode_pencairan),
        };
    }
}
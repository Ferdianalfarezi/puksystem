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
        'jenis_pengeluaran',
        'tahun',
        'tanggal',
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
        'tanggal' => 'date',
    ];

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

    // Scopes
    public function scopeForBidang($query, $bidangId)
    {
        return $query->where('bidang_id', $bidangId);
    }

    public function scopeByJenisPengeluaran($query, $jenis)
    {
        return $query->where('jenis_pengeluaran', $jenis);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // Helper method untuk badge warna
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

    // Format angka
    public function getAnggaranFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->anggaran, 0, ',', '.');
    }

    // Format tanggal
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d M Y') : '-';
    }
}
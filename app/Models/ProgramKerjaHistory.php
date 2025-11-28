<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramKerjaHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_kerja_id',
        'tanggal_program',
        'status_dari',
        'status_ke',
        'catatan',
        'dilakukan_oleh',
        'dilakukan_pada',
        'data_snapshot',
    ];

    protected $casts = [
        'dilakukan_pada' => 'datetime',
        'tanggal_program' => 'date',
        'data_snapshot' => 'array',
    ];

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class);
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    public function getStatusDariLabelAttribute(): string
    {
        return $this->getStatusLabel($this->status_dari);
    }

    public function getStatusKeLabelAttribute(): string
    {
        return $this->getStatusLabel($this->status_ke);
    }

    private function getStatusLabel(?string $status): string
    {
        if (!$status) return '-';
        
        return match($status) {
            'draft' => 'Draft',
            'menunggu_konfirmasi_bendahara' => 'Menunggu Bendahara',
            'menunggu_approval_ketua' => 'Menunggu Ketua',
            'menunggu_pencairan' => 'Menunggu Pencairan',
            'dicairkan' => 'Dicairkan',
            'ditolak_bendahara' => 'Ditolak Bendahara',
            'ditolak_ketua' => 'Ditolak Ketua',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public function scopeForProgram($query, $programKerjaId)
    {
        return $query->where('program_kerja_id', $programKerjaId);
    }
}
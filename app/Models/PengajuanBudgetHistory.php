<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanBudgetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_budget_id',
        'tanggal_pengajuan',
        'status_dari',
        'status_ke',
        'catatan',
        'dilakukan_oleh',
        'dilakukan_pada',
        'data_snapshot',
    ];

    protected $casts = [
        'dilakukan_pada' => 'datetime',
        'data_snapshot' => 'array',
        'tanggal_pengajuan' => 'date',
    ];

    public function pengajuanBudget(): BelongsTo
    {
        return $this->belongsTo(PengajuanBudget::class);
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}
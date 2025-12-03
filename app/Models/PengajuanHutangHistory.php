<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanHutangHistory extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_hutang_history';

    protected $fillable = [
        'pengajuan_hutang_id',
        'status_dari',
        'status_ke',
        'catatan',
        'data_snapshot',
        'dilakukan_oleh',
        'dilakukan_pada',
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'dilakukan_pada' => 'datetime',
    ];

    // Relations
    public function pengajuanHutang(): BelongsTo
    {
        return $this->belongsTo(PengajuanHutang::class);
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}
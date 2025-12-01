<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistoryKas extends Model
{
    protected $table = 'history_kas';

    protected $fillable = [
        'kas_id',
        'jenis',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'sumber',
        'referable_id',
        'referable_type',
        'keterangan',
        'dilakukan_oleh',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'tanggal_transaksi' => 'datetime',
    ];

    public function kas(): BelongsTo
    {
        return $this->belongsTo(Kas::class);
    }

    public function referable(): MorphTo
    {
        return $this->morphTo();
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}
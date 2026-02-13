<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensasiHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispensasi_id',
        'status_dari',
        'status_ke',
        'catatan',
        'dilakukan_oleh',
        'dilakukan_pada',
    ];

    protected $casts = [
        'dilakukan_pada' => 'datetime',
    ];

    public function dispensasi(): BelongsTo
    {
        return $this->belongsTo(Dispensasi::class);
    }

    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}
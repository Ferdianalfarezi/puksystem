<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kas extends Model
{
    protected $table = 'kas';

    protected $fillable = [
        'saldo',
        'keterangan',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(HistoryKas::class);
    }

    /**
     * Get atau create kas global (singleton pattern)
     */
    public static function getGlobal(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['saldo' => 0, 'keterangan' => 'Kas Global']
        );
    }

    /**
     * Tambah saldo (setoran)
     */
    public function tambahSaldo(float $jumlah, string $keterangan, int $userId, $referable = null): HistoryKas
    {
        $saldoSebelum = $this->saldo;
        $saldoSesudah = $saldoSebelum + $jumlah;

        $history = $this->histories()->create([
            'jenis' => 'masuk',
            'jumlah' => $jumlah,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'sumber' => 'setoran',
            'referable_id' => $referable?->id,
            'referable_type' => $referable ? get_class($referable) : null,
            'keterangan' => $keterangan,
            'dilakukan_oleh' => $userId,
            'tanggal_transaksi' => now(),
        ]);

        $this->update(['saldo' => $saldoSesudah]);

        return $history;
    }

    /**
     * Kurangi saldo (pencairan)
     */
    public function kurangiSaldo(float $jumlah, string $keterangan, int $userId, $referable = null): HistoryKas
    {
        $saldoSebelum = $this->saldo;
        $saldoSesudah = $saldoSebelum - $jumlah;

        $history = $this->histories()->create([
            'jenis' => 'keluar',
            'jumlah' => $jumlah,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'sumber' => 'pencairan',
            'referable_id' => $referable?->id,
            'referable_type' => $referable ? get_class($referable) : null,
            'keterangan' => $keterangan,
            'dilakukan_oleh' => $userId,
            'tanggal_transaksi' => now(),
        ]);

        $this->update(['saldo' => $saldoSesudah]);

        return $history;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHutang;
use App\Models\PembayaranHutang;
use App\Models\PengajuanHutangHistory;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranHutangController extends Controller
{
    public function bayar(Request $request, PengajuanHutang $pengajuanHutang)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        // Check authorization (superadmin dan bendahara bisa bayar)
        if (!in_array($userRole, ['superadmin', 'bendahara'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if (!$pengajuanHutang->canBePaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Hutang ini tidak dapat dibayar. Pastikan status hutang adalah "dicairkan" dan masih memiliki sisa hutang.'
            ]);
        }

        $validated = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1|max:' . $pengajuanHutang->sisa_hutang,
            'metode_pembayaran' => 'required|in:transfer_bank,tunai,cek,giro',
            'nomor_referensi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'jumlah_bayar.required' => 'Jumlah pembayaran wajib diisi.',
            'jumlah_bayar.min' => 'Jumlah pembayaran minimal Rp 1.',
            'jumlah_bayar.max' => 'Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp ' . number_format($pengajuanHutang->sisa_hutang, 0, ',', '.') . ').',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $kasGlobal = Kas::getGlobal();
            $saldoSebelum = $kasGlobal->saldo;
            $sisaHutangSebelum = $pengajuanHutang->sisa_hutang;
            $sisaHutangSesudah = $sisaHutangSebelum - $validated['jumlah_bayar'];
            $saldoSesudah = $saldoSebelum + $validated['jumlah_bayar'];

            // Create pembayaran record
            $pembayaran = PembayaranHutang::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'tanggal_bayar' => now(),
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'dibayar_oleh' => Auth::id(),
            ]);

            // Tambah saldo kas (pembayaran hutang = pemasukan)
            $historyKas = $kasGlobal->tambahSaldo(
                jumlah: $validated['jumlah_bayar'],
                keterangan: "Pembayaran Hutang: {$pengajuanHutang->nama} ({$pengajuanHutang->bidang->nama})",
                userId: Auth::id(),
                referable: $pembayaran
            );

            // Update pembayaran dengan history_kas_id
            $pembayaran->update(['history_kas_id' => $historyKas->id]);

            // Update sisa_hutang
            $pengajuanHutang->update(['sisa_hutang' => $sisaHutangSesudah]);

            // Jika lunas, update status
            $statusBaru = $pengajuanHutang->status;
            if ($sisaHutangSesudah == 0) {
                $statusBaru = 'lunas';
                $pengajuanHutang->update(['status' => 'lunas']);
            }

            // Create history
            PengajuanHutangHistory::create([
                'pengajuan_hutang_id' => $pengajuanHutang->id,
                'status_dari' => 'dicairkan',
                'status_ke' => $statusBaru,
                'catatan' => 'Pembayaran sebesar Rp ' . number_format($validated['jumlah_bayar'], 0, ',', '.') 
                          . ' via ' . $this->getMetodePembayaranLabel($validated['metode_pembayaran'])
                          . '. Sisa hutang: Rp ' . number_format($sisaHutangSesudah, 0, ',', '.')
                          . ($sisaHutangSesudah == 0 ? ' (LUNAS)' : '')
                          . '. Saldo kas: Rp ' . number_format($saldoSesudah, 0, ',', '.'),
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $pengajuanHutang->nama,
                    'jumlah_hutang' => $pengajuanHutang->jumlah,
                    'jumlah_bayar' => $validated['jumlah_bayar'],
                    'sisa_hutang_sebelum' => $sisaHutangSebelum,
                    'sisa_hutang_sesudah' => $sisaHutangSesudah,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'saldo_kas_sebelum' => $saldoSebelum,
                    'saldo_kas_sesudah' => $saldoSesudah,
                    'bidang' => $pengajuanHutang->bidang->nama,
                    'tahun' => $pengajuanHutang->tahun,
                    'is_lunas' => $sisaHutangSesudah == 0,
                ],
            ]);

            DB::commit();

            $message = 'Pembayaran berhasil diproses. Sisa hutang: Rp ' . number_format($sisaHutangSesudah, 0, ',', '.');
            if ($sisaHutangSesudah == 0) {
                $message = 'Pembayaran berhasil diproses.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'sisa_hutang' => $sisaHutangSesudah,
                    'is_lunas' => $sisaHutangSesudah == 0,
                    'saldo_kas' => $saldoSesudah,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getMetodePembayaranLabel($metode)
    {
        return match($metode) {
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
            default => $metode,
        };
    }

    public function show(PembayaranHutang $pembayaranHutang)
    {
        $pembayaranHutang->load(['pengajuanHutang', 'dibayarOleh']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pembayaranHutang->id,
                    'jumlah_bayar' => $pembayaranHutang->jumlah_bayar,
                    'tanggal_bayar' => $pembayaranHutang->tanggal_bayar->format('d M Y'),
                    'metode_pembayaran' => $pembayaranHutang->metode_pembayaran_label,
                    'nomor_referensi' => $pembayaranHutang->nomor_referensi,
                    'catatan' => $pembayaranHutang->catatan,
                    'dibayar_oleh' => $pembayaranHutang->dibayarOleh->name,
                    'pengajuan_hutang' => [
                        'id' => $pembayaranHutang->pengajuanHutang->id,
                        'nama' => $pembayaranHutang->pengajuanHutang->nama,
                        'jumlah' => $pembayaranHutang->pengajuanHutang->jumlah,
                        'sisa_hutang' => $pembayaranHutang->pengajuanHutang->sisa_hutang,
                    ]
                ]
            ]);
        }

        return view('pembayaran-hutang.detail', compact('pembayaranHutang'));
    }
}
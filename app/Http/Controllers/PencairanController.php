<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\Pencairan;
use App\Models\ProgramKerjaHistory;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index()
    {
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua'])
            ->where('status', 'menunggu_pencairan')
            ->latest('reviewed_at_ketua')
            ->paginate(10);

        // Ambil saldo kas untuk ditampilkan di view
        $kasGlobal = Kas::getGlobal();

        return view('bendahara.pencairan.index', compact('programKerjas', 'kasGlobal'));
    }

    

    public function cairkan(Request $request, ProgramKerja $programKerja)
    {
        if ($programKerja->status !== 'menunggu_pencairan') {
            return response()->json([
                'success' => false,
                'message' => 'Program kerja ini tidak dapat dicairkan.'
            ]);
        }

        $validated = $request->validate([
            'jumlah_dicairkan' => 'required|numeric|min:0|max:' . $programKerja->anggaran,
            'metode_pencairan' => 'required|in:transfer,tunai,cek',
            'nomor_referensi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'jumlah_dicairkan.required' => 'Jumlah pencairan wajib diisi.',
            'jumlah_dicairkan.max' => 'Jumlah pencairan tidak boleh melebihi anggaran yang disetujui.',
            'metode_pencairan.required' => 'Metode pencairan wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $kasGlobal = Kas::getGlobal();
            $statusLama = $programKerja->status;
            $saldoSebelum = $kasGlobal->saldo;
            $saldoSesudah = $saldoSebelum - $validated['jumlah_dicairkan'];

            // Create pencairan record
            $pencairan = Pencairan::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                'tanggal_pencairan' => now(),
                'metode_pencairan' => $validated['metode_pencairan'],
                'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'dicairkan_oleh' => Auth::id(),
            ]);

            // Kurangi saldo kas dan create history
            $historyKas = $kasGlobal->kurangiSaldo(
                jumlah: $validated['jumlah_dicairkan'],
                keterangan: "Pencairan: {$programKerja->nama} ({$programKerja->bidang->nama})",
                userId: Auth::id(),
                referable: $pencairan
            );

            // Update pencairan dengan history_kas_id
            $pencairan->update(['history_kas_id' => $historyKas->id]);

            // Update program kerja status
            $programKerja->update(['status' => 'dicairkan']);

            // Create program kerja history
            ProgramKerjaHistory::create([
                'program_kerja_id' => $programKerja->id,
                'tanggal_program' => $programKerja->tanggal,
                'status_dari' => $statusLama,
                'status_ke' => 'dicairkan',
                'catatan' => 'Dana dicairkan sebesar Rp ' . number_format($validated['jumlah_dicairkan'], 0, ',', '.') 
                          . ' via ' . $validated['metode_pencairan'] 
                          . '. Saldo kas: Rp ' . number_format($saldoSesudah, 0, ',', '.'),
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $programKerja->nama,
                    'anggaran' => $programKerja->anggaran,
                    'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                    'metode_pencairan' => $validated['metode_pencairan'],
                    'saldo_kas_sebelum' => $saldoSebelum,
                    'saldo_kas_sesudah' => $saldoSesudah,
                    'bidang' => $programKerja->bidang->nama,
                    'tahun' => $programKerja->tahun,
                    'tanggal' => $programKerja->tanggal ? $programKerja->tanggal->format('Y-m-d') : null,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dana berhasil dicairkan. Saldo kas saat ini: Rp ' . number_format($saldoSesudah, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
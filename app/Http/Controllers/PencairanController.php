<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\PengajuanBudget;
use App\Models\PengajuanHutang; // ✅ TAMBAHKAN
use App\Models\Pencairan;
use App\Models\PencairanBudget;
use App\Models\ProgramKerjaHistory;
use App\Models\PengajuanBudgetHistory;
use App\Models\PengajuanHutangHistory; // ✅ TAMBAHKAN
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PencairanController extends Controller
{
    public function index()
    {
        // Ambil Program Kerja yang menunggu pencairan
        $programKerjas = ProgramKerja::with(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua'])
            ->where('status', 'menunggu_pencairan')
            ->get()
            ->map(function($pk) {
                return [
                    'id' => $pk->id,
                    'type' => 'program_kerja',
                    'bidang' => $pk->bidang->nama,
                    'nama' => $pk->nama,
                    'jenis_pengeluaran' => $pk->jenis_pengeluaran,
                    'anggaran' => $pk->anggaran,
                    'tanggal' => $pk->tanggal,
                    'submitted_by' => $pk->submittedBy->name ?? '-',
                    'reviewed_at_ketua' => $pk->reviewed_at_ketua,
                    'model' => $pk,
                ];
            });

        // Ambil Pengajuan Budget yang menunggu pencairan
        $pengajuanBudgets = PengajuanBudget::with(['bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua'])
            ->where('status', 'menunggu_pencairan')
            ->get()
            ->map(function($pb) {
                return [
                    'id' => $pb->id,
                    'type' => 'pengajuan_budget',
                    'bidang' => $pb->bidang->nama,
                    'nama' => $pb->nama,
                    'jenis_pengeluaran' => $pb->jenis_pengeluaran,
                    'anggaran' => $pb->anggaran,
                    'tanggal' => $pb->tanggal,
                    'submitted_by' => $pb->submittedBy->name ?? '-',
                    'reviewed_at_ketua' => $pb->reviewed_at_ketua,
                    'model' => $pb,
                ];
            });

        // ✅ TAMBAHKAN: Ambil Pengajuan Hutang yang menunggu pencairan
        $pengajuanHutangs = PengajuanHutang::with(['user', 'bidang', 'submittedBy', 'reviewedByBendahara', 'reviewedByKetua'])
            ->where('status', 'menunggu_pencairan')
            ->get()
            ->map(function($ph) {
                return [
                    'id' => $ph->id,
                    'type' => 'pengajuan_hutang',
                    'bidang' => $ph->bidang->nama,
                    'nama' => $ph->nama . ' (Hutang)',
                    'jenis_pengeluaran' => 'Hutang',
                    'anggaran' => $ph->jumlah,
                    'tanggal' => $ph->tanggal,
                    'submitted_by' => $ph->submittedBy->name ?? '-',
                    'reviewed_at_ketua' => $ph->reviewed_at_ketua,
                    'model' => $ph,
                ];
            });

        // Gabungkan dan sort by reviewed_at_ketua
        $allPencairan = $programKerjas->concat($pengajuanBudgets)->concat($pengajuanHutangs)
            ->sortByDesc('reviewed_at_ketua')
            ->values();

        // Manual pagination
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $total = $allPencairan->count();
        $items = $allPencairan->forPage($currentPage, $perPage);

        $pencairanPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Ambil saldo kas
        $kasGlobal = Kas::getGlobal();

        return view('bendahara.pencairan.index', compact('pencairanPaginated', 'kasGlobal'));
    }

    public function cairkan(Request $request, $type, $id)
    {
        // Tentukan model berdasarkan type
        if ($type === 'program_kerja') {
            $item = ProgramKerja::findOrFail($id);
            $pencairanModel = Pencairan::class;
            $historyModel = ProgramKerjaHistory::class;
            $foreignKey = 'program_kerja_id';
            $isHutang = false;
        } elseif ($type === 'pengajuan_budget') {
            $item = PengajuanBudget::findOrFail($id);
            $pencairanModel = PencairanBudget::class;
            $historyModel = PengajuanBudgetHistory::class;
            $foreignKey = 'pengajuan_budget_id';
            $isHutang = false;
        } elseif ($type === 'pengajuan_hutang') { // ✅ TAMBAHKAN
            $item = PengajuanHutang::findOrFail($id);
            $historyModel = PengajuanHutangHistory::class;
            $foreignKey = 'pengajuan_hutang_id';
            $isHutang = true;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipe tidak valid.'
            ], 400);
        }

        if ($item->status !== 'menunggu_pencairan') {
            return response()->json([
                'success' => false,
                'message' => 'Item ini tidak dapat dicairkan.'
            ]);
        }

        $jumlahField = $isHutang ? 'jumlah' : 'anggaran';
        $validated = $request->validate([
            'jumlah_dicairkan' => 'required|numeric|min:0|max:' . $item->$jumlahField,
            'metode_pencairan' => 'required|in:transfer_bank,tunai,cek,giro',
            'nomor_referensi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'jumlah_dicairkan.required' => 'Jumlah pencairan wajib diisi.',
            'jumlah_dicairkan.max' => 'Jumlah pencairan tidak boleh melebihi jumlah yang disetujui.',
            'metode_pencairan.required' => 'Metode pencairan wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $kasGlobal = Kas::getGlobal();
            $statusLama = $item->status;
            $saldoSebelum = $kasGlobal->saldo;
            
            // ✅ LOGIC BERBEDA: Hutang MENGURANGI kas, bukan menambah
            if ($isHutang) {
                $saldoSesudah = $saldoSebelum - $validated['jumlah_dicairkan'];
                
                // Kurangi saldo kas
                $historyKas = $kasGlobal->kurangiSaldo(
                    jumlah: $validated['jumlah_dicairkan'],
                    keterangan: "Pencairan Hutang: {$item->nama} ({$item->bidang->nama})",
                    userId: Auth::id(),
                    referable: $item
                );
            } else {
                $saldoSesudah = $saldoSebelum - $validated['jumlah_dicairkan'];
                
                // Create pencairan record (untuk program kerja & budget)
                $pencairan = $pencairanModel::create([
                    $foreignKey => $item->id,
                    'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                    'tanggal_pencairan' => now(),
                    'metode_pencairan' => $validated['metode_pencairan'],
                    'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                    'dicairkan_oleh' => Auth::id(),
                ]);

                // Kurangi saldo kas
                $historyKas = $kasGlobal->kurangiSaldo(
                    jumlah: $validated['jumlah_dicairkan'],
                    keterangan: "Pencairan: {$item->nama} ({$item->bidang->nama})",
                    userId: Auth::id(),
                    referable: $pencairan
                );

                // Update pencairan dengan history_kas_id
                if ($type === 'program_kerja') {
                    $pencairan->update(['history_kas_id' => $historyKas->id]);
                }
            }

            // Update status
            $item->update(['status' => 'dicairkan']);

            // Create history
            $historyData = [
                $foreignKey => $item->id,
                'status_dari' => $statusLama,
                'status_ke' => 'dicairkan',
                'catatan' => 'Dana dicairkan sebesar Rp ' . number_format($validated['jumlah_dicairkan'], 0, ',', '.') 
                          . ' via ' . $this->getMetodePencairanLabel($validated['metode_pencairan'])
                          . '. Saldo kas: Rp ' . number_format($saldoSesudah, 0, ',', '.'),
                'dilakukan_oleh' => Auth::id(),
                'dilakukan_pada' => now(),
                'data_snapshot' => [
                    'nama' => $item->nama,
                    $jumlahField => $item->$jumlahField,
                    'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                    'metode_pencairan' => $validated['metode_pencairan'],
                    'saldo_kas_sebelum' => $saldoSebelum,
                    'saldo_kas_sesudah' => $saldoSesudah,
                    'bidang' => $item->bidang->nama,
                    'tahun' => $item->tahun,
                    'tanggal' => $item->tanggal ? $item->tanggal->format('Y-m-d') : null,
                ],
            ];

            $historyModel::create($historyData);

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

    private function getMetodePencairanLabel($metode)
    {
        return match($metode) {
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
            default => $metode,
        };
    }
}
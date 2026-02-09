<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\PengajuanBudget;
use App\Models\PengajuanHutang;
use App\Models\Pencairan;
use App\Models\PencairanBudget;
use App\Models\ProgramKerjaHistory;
use App\Models\PengajuanBudgetHistory;
use App\Models\PengajuanHutangHistory;
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

        // Ambil Pengajuan Hutang yang menunggu pencairan
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
        } elseif ($type === 'pengajuan_hutang') {
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
            $saldoSesudah = $saldoSebelum - $validated['jumlah_dicairkan'];
            
            $pencairan = null; // Initialize variabel pencairan
            
            if ($isHutang) {
                // Untuk hutang, langsung kurangi kas tanpa create record pencairan
                $historyKas = $kasGlobal->kurangiSaldo(
                    jumlah: $validated['jumlah_dicairkan'],
                    keterangan: "Pencairan Hutang: {$item->nama} ({$item->bidang->nama})",
                    userId: Auth::id(),
                    referable: $item
                );
            } else {
                // Untuk Program Kerja & Budget: Create record pencairan dulu
                $pencairanData = [
                    $foreignKey => $item->id,
                    'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                    'tanggal_pencairan' => now(),
                    'metode_pencairan' => $validated['metode_pencairan'],
                    'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                    'dicairkan_oleh' => Auth::id(),
                ];

                // Tambahkan tanggal_program hanya untuk Pencairan (Program Kerja)
                if ($type === 'program_kerja') {
                    $pencairanData['tanggal_program'] = $item->tanggal;
                }

                $pencairan = $pencairanModel::create($pencairanData);

                // Kurangi saldo kas
                $historyKas = $kasGlobal->kurangiSaldo(
                    jumlah: $validated['jumlah_dicairkan'],
                    keterangan: "Pencairan: {$item->nama} ({$item->bidang->nama})",
                    userId: Auth::id(),
                    referable: $pencairan
                );

                // Update pencairan dengan history_kas_id (hanya untuk program_kerja)
                if ($type === 'program_kerja') {
                    $pencairan->update(['history_kas_id' => $historyKas->id]);
                }
            }

            // Update status item
            $item->update(['status' => 'dicairkan']);

            // ✅ CREATE HISTORY BERDASARKAN DATA DARI PENCAIRAN
            $this->createHistory(
                type: $type,
                item: $item,
                pencairan: $pencairan,
                validated: $validated,
                statusLama: $statusLama,
                saldoSebelum: $saldoSebelum,
                saldoSesudah: $saldoSesudah,
                historyModel: $historyModel,
                foreignKey: $foreignKey,
                isHutang: $isHutang
            );

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

    /**
     * Create history record berdasarkan data pencairan
     */
    private function createHistory(
        string $type,
        $item,
        $pencairan,
        array $validated,
        string $statusLama,
        float $saldoSebelum,
        float $saldoSesudah,
        string $historyModel,
        string $foreignKey,
        bool $isHutang
    ): void
    {
        $jumlahField = $isHutang ? 'jumlah' : 'anggaran';
        
        // Base history data
        $historyData = [
            $foreignKey => $item->id,
            'status_dari' => $statusLama,
            'status_ke' => 'dicairkan',
            'dilakukan_oleh' => Auth::id(),
            'dilakukan_pada' => now(),
        ];

        // ✅ AMBIL DATA DARI PENCAIRAN (kalau bukan hutang)
        if (!$isHutang && $pencairan) {
            $historyData['catatan'] = 'Dana dicairkan sebesar Rp ' . number_format($pencairan->jumlah_dicairkan, 0, ',', '.') 
                      . ' via ' . $pencairan->metode_pencairan_label
                      . ($pencairan->nomor_referensi ? ' (Ref: ' . $pencairan->nomor_referensi . ')' : '')
                      . '. Saldo kas: Rp ' . number_format($saldoSesudah, 0, ',', '.');

            $historyData['data_snapshot'] = [
                'nama' => $item->nama,
                $jumlahField => $item->$jumlahField,
                'jumlah_dicairkan' => $pencairan->jumlah_dicairkan,
                'tanggal_pencairan' => $pencairan->tanggal_pencairan->format('Y-m-d H:i:s'),
                'metode_pencairan' => $pencairan->metode_pencairan,
                'metode_pencairan_label' => $pencairan->metode_pencairan_label,
                'nomor_referensi' => $pencairan->nomor_referensi,
                'catatan_pencairan' => $pencairan->catatan,
                'saldo_kas_sebelum' => $saldoSebelum,
                'saldo_kas_sesudah' => $saldoSesudah,
                'bidang' => $item->bidang->nama,
                'tahun' => $item->tahun,
                'tanggal' => $item->tanggal?->format('Y-m-d'),
                'dicairkan_oleh' => $pencairan->dicairkanOleh->name ?? '-',
            ];

            // Tambahkan tanggal_program untuk ProgramKerjaHistory
            if ($type === 'program_kerja') {
                $historyData['tanggal_program'] = $pencairan->tanggal_program;
            }

            // Tambahkan tanggal_pengajuan untuk PengajuanBudgetHistory
            if ($type === 'pengajuan_budget') {
                $historyData['tanggal_pengajuan'] = $item->tanggal;
            }
        } else {
            // Untuk hutang (tanpa record pencairan)
            $historyData['catatan'] = 'Hutang dicairkan sebesar Rp ' . number_format($validated['jumlah_dicairkan'], 0, ',', '.') 
                      . ' via ' . $this->getMetodePencairanLabel($validated['metode_pencairan'])
                      . ($validated['nomor_referensi'] ? ' (Ref: ' . $validated['nomor_referensi'] . ')' : '')
                      . '. Saldo kas: Rp ' . number_format($saldoSesudah, 0, ',', '.');

            $historyData['data_snapshot'] = [
                'nama' => $item->nama,
                'jumlah' => $item->jumlah,
                'jumlah_dicairkan' => $validated['jumlah_dicairkan'],
                'tanggal_pencairan' => now()->format('Y-m-d H:i:s'),
                'metode_pencairan' => $validated['metode_pencairan'],
                'metode_pencairan_label' => $this->getMetodePencairanLabel($validated['metode_pencairan']),
                'nomor_referensi' => $validated['nomor_referensi'] ?? null,
                'catatan_pencairan' => $validated['catatan'] ?? null,
                'saldo_kas_sebelum' => $saldoSebelum,
                'saldo_kas_sesudah' => $saldoSesudah,
                'bidang' => $item->bidang->nama,
                'tahun' => $item->tahun,
                'tanggal' => $item->tanggal?->format('Y-m-d'),
                'dicairkan_oleh' => Auth::user()->name,
            ];
        }

        // Create history record
        $historyModel::create($historyData);
    }

    private function getMetodePencairanLabel($metode)
    {
        return match($metode) {
            'transfer_bank' => 'Transfer Bank',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
            default => ucfirst(str_replace('_', ' ', $metode)),
        };
    }
}
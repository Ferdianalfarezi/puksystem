<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\HistoryKas;
use App\Exports\KasExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KasController extends Controller
{
    public function index(Request $request)
{
    $kasGlobal = Kas::getGlobal();

    // === FILTER PARAMETER ===
    $year = $request->get('year');
    $month = $request->get('month');

    // === BASE QUERY ===
    $query = HistoryKas::with(['dilakukanOleh', 'referable'])
        ->where('kas_id', $kasGlobal->id);

    if ($year) {
        $query->whereYear('tanggal_transaksi', $year);
    }

    if ($month) {
        $query->whereMonth('tanggal_transaksi', $month);
    }

    // Untuk summary (harus clone biar tidak ketimpa)
    $summaryQuery = clone $query;

    // === PAGINATION ===
    $histories = $query->latest('tanggal_transaksi')
        ->paginate(20)
        ->appends($request->all());

    // === SUMMARY (benar-benar mengikuti filter) ===
    $totalMasuk = (clone $summaryQuery)
        ->where('jenis', 'masuk')
        ->sum('jumlah');

    $totalKeluar = (clone $summaryQuery)
        ->where('jenis', 'keluar')
        ->sum('jumlah');

    $totalTransaksi = (clone $summaryQuery)->count();

    // === SALDO AWAL & AKHIR PER FILTER ===
    $firstTransaction = (clone $summaryQuery)
        ->orderBy('tanggal_transaksi', 'asc')
        ->first();

    $lastTransaction = (clone $summaryQuery)
        ->orderBy('tanggal_transaksi', 'desc')
        ->first();

    $saldoAwal = $firstTransaction
        ? $firstTransaction->saldo_sebelum
        : $kasGlobal->saldo;

    $saldoAkhir = $lastTransaction
        ? $lastTransaction->saldo_sesudah
        : $kasGlobal->saldo;

    // === LIST TAHUN ===
    $availableYears = HistoryKas::where('kas_id', $kasGlobal->id)
        ->selectRaw('YEAR(tanggal_transaksi) AS year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    if (!$availableYears->contains(now()->year)) {
        $availableYears->prepend(now()->year);
    }

    // === LIST BULAN ===
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    return view('history.kas', compact(
        'kasGlobal',
        'histories',
        'totalMasuk',
        'totalKeluar',
        'totalTransaksi',
        'saldoAwal',
        'saldoAkhir',
        'year',
        'month',
        'availableYears',
        'months'
    ));
}


    public function export(Request $request)
    {
        $kasGlobal = Kas::getGlobal();
        
        // Get filter parameters
        $year = $request->get('year');
        $month = $request->get('month');
        
        // Build query for summary calculation
        $query = HistoryKas::where('kas_id', $kasGlobal->id);
        
        if ($year) {
            $query->whereYear('tanggal_transaksi', $year);
        }
        
        if ($month) {
            $query->whereMonth('tanggal_transaksi', $month);
        }
        
        // Calculate summary
        $totalMasuk = $query->where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = $query->where('jenis', 'keluar')->sum('jumlah');
        
        // Get saldo awal & akhir
        $saldoAwal = $kasGlobal->saldo;
        $saldoAkhir = $kasGlobal->saldo;
        
        $allData = $query->latest('tanggal_transaksi')->get();
        if ($allData->isNotEmpty()) {
            $saldoAwal = $allData->last()->saldo_sebelum;
            $saldoAkhir = $allData->first()->saldo_sesudah;
        }
        
        // Generate filename
        $filename = 'Laporan_Kas_' . $this->getPeriodLabel($year, $month) . '_' . now()->format('YmdHis') . '.xlsx';
        
        return Excel::download(
            new KasExport($kasGlobal->id, $year, $month, $totalMasuk, $totalKeluar, $saldoAwal, $saldoAkhir), 
            $filename
        );
    }

    public function setor(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:500',
        ], [
            'jumlah.required' => 'Jumlah setoran wajib diisi.',
            'jumlah.min' => 'Jumlah setoran minimal Rp 1.',
            'keterangan.required' => 'Keterangan wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $kasGlobal = Kas::getGlobal();
            
            $historyKas = $kasGlobal->tambahSaldo(
                jumlah: $validated['jumlah'],
                keterangan: $validated['keterangan'],
                userId: Auth::id()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setoran berhasil. Saldo kas saat ini: Rp ' . number_format($kasGlobal->fresh()->saldo, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPeriodLabel($year, $month)
    {
        if (!$year && !$month) {
            return 'Semua_Periode';
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($year && $month) {
            return $months[$month] . '_' . $year;
        }

        if ($year) {
            return 'Tahun_' . $year;
        }

        return 'Semua_Periode';
    }
}
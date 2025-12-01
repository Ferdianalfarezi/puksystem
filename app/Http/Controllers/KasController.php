<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\HistoryKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    public function index()
    {
        $kasGlobal = Kas::getGlobal();
        
        $histories = HistoryKas::with(['dilakukanOleh', 'referable'])
            ->where('kas_id', $kasGlobal->id)
            ->latest('tanggal_transaksi')
            ->paginate(20);

        // Return ke view yang benar: history/kas
        return view('history.kas', compact('kasGlobal', 'histories'));
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
}

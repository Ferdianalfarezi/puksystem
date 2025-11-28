<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\ProgramKerjaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        $query = ProgramKerja::with(['bidang', 'pencairan', 'histories']);

        if ($userRole === 'admin') {
            $query->where('bidang_id', $user->bidang_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if (in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua'])) {
            if ($request->has('bidang_id') && $request->bidang_id != '') {
                $query->where('bidang_id', $request->bidang_id);
            }
        }

        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }

        $programKerjas = $query->latest()->paginate(15);

        if (in_array($userRole, ['superadmin', 'bendahara', 'sekretaris', 'ketua'])) {
            $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        } else {
            $bidangs = \App\Models\Bidang::where('id', $user->bidang_id)->get();
        }
        
        $tahuns = ProgramKerja::distinct()->pluck('tahun')->sort()->values();

        return view('history.program-kerja', compact('programKerjas', 'bidangs', 'tahuns'));
    }

    public function show(ProgramKerja $programKerja)
    {
        $user = Auth::user();
        $userRole = $user->role->nama ?? '';
        
        if ($userRole === 'admin') {
            if ($programKerja->bidang_id !== $user->bidang_id) {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $programKerja->load([
            'bidang', 
            'submittedBy', 
            'reviewedByBendahara', 
            'reviewedByKetua',
            'pencairan.dicairkanOleh',
            'histories.dilakukanOleh'
        ]);

        return view('history.detail', compact('programKerja'));
    }
}
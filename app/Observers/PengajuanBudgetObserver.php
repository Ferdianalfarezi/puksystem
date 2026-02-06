<?php

namespace App\Observers;

use App\Models\PengajuanBudget;

class PengajuanBudgetObserver
{
    /**
     * Handle the PengajuanBudget "created" event.
     */
    public function created(PengajuanBudget $pengajuanBudget): void
    {
        $this->syncStatusToProgramKerja($pengajuanBudget);
    }

    /**
     * Handle the PengajuanBudget "updated" event.
     */
    public function updated(PengajuanBudget $pengajuanBudget): void
    {
        $this->syncStatusToProgramKerja($pengajuanBudget);
    }

    /**
     * Sync status dari PengajuanBudget ke ProgramKerja
     */
    private function syncStatusToProgramKerja(PengajuanBudget $pengajuanBudget): void
    {
        // Cek apakah ini tipe program_kerja DAN ada program_kerja_id
        if ($pengajuanBudget->jenis === 'program_kerja' && $pengajuanBudget->program_kerja_id) {
            $programKerja = $pengajuanBudget->programKerja;
            
            if ($programKerja) {
                $programKerja->update([
                    'status' => $pengajuanBudget->status
                ]);
            }
        }
    }
}
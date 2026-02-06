<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\KetuaController;
use App\Http\Controllers\PencairanController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PengajuanBudgetController;
use App\Http\Controllers\BendaharaPengajuanController;
use App\Http\Controllers\KetuaPengajuanController;
use App\Http\Controllers\HistoryPengajuanController;
use App\Http\Controllers\PengajuanHutangController;
use App\Http\Controllers\BendaharaHutangController;
use App\Http\Controllers\KetuaHutangController;
use App\Http\Controllers\PembayaranHutangController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventAttendanceController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    
    // Bidang Management Routes  
    Route::resource('bidangs', BidangController::class);
    
    // User Management Routes
    Route::get('/users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::resource('users', UserController::class);

    // Route untuk Admin Bidang - Program Kerja
    Route::resource('program-kerja', ProgramKerjaController::class);
    Route::post('program-kerja/{programKerja}/submit', [ProgramKerjaController::class, 'submit'])
        ->name('program-kerja.submit');

    // Route untuk Admin Bidang - Pengajuan Budget
    Route::resource('pengajuan-budget', PengajuanBudgetController::class);
    Route::post('pengajuan-budget/{pengajuanBudget}/submit', [PengajuanBudgetController::class, 'submit'])
        ->name('pengajuan-budget.submit');

    // Route untuk Pengajuan Hutang
    Route::resource('pengajuan-hutang', PengajuanHutangController::class);
    Route::post('pengajuan-hutang/{pengajuanHutang}/submit', [PengajuanHutangController::class, 'submit'])
        ->name('pengajuan-hutang.submit');
    
    // Route untuk List Hutang Aktif & Pembayaran
    Route::get('list-hutang', [PengajuanHutangController::class, 'listHutangAktif'])->name('list-hutang');
    Route::post('list-hutang/{pengajuanHutang}/bayar', [PembayaranHutangController::class, 'bayar'])->name('hutang.bayar');

    // Route untuk Events Management
    Route::resource('events', EventController::class);
    
    // Route untuk Event Attendance (Daftar Hadir)
    Route::prefix('events/{event}')->name('events.attendance.')->group(function () {
        Route::get('/attendance', [EventAttendanceController::class, 'index'])->name('index');
        Route::post('/scan', [EventAttendanceController::class, 'scan'])->name('scan');
        Route::get('/list', [EventAttendanceController::class, 'list'])->name('list');
        Route::get('/export', [EventAttendanceController::class, 'export'])->name('export');
    });

    // ========================================
    // ROUTE BENDAHARA - SEMUA PAKE PREFIX JELAS
    // ========================================
    Route::prefix('bendahara')->name('bendahara.')->group(function () {
        
        // PENGAJUAN BUDGET
        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
            Route::get('/', [BendaharaPengajuanController::class, 'index'])->name('index');
            Route::get('/{pengajuanBudget}', [BendaharaPengajuanController::class, 'show'])->name('show');
            Route::post('/{pengajuanBudget}/approve', [BendaharaPengajuanController::class, 'approve'])->name('approve');
            Route::post('/{pengajuanBudget}/reject', [BendaharaPengajuanController::class, 'reject'])->name('reject');
        });
        
        // PENGAJUAN HUTANG
        Route::prefix('hutang')->name('hutang.')->group(function () {
            Route::get('/', [BendaharaHutangController::class, 'index'])->name('index');
            Route::get('/{pengajuanHutang}', [BendaharaHutangController::class, 'show'])->name('show');
            Route::post('/{pengajuanHutang}/approve', [BendaharaHutangController::class, 'approve'])->name('approve');
            Route::post('/{pengajuanHutang}/reject', [BendaharaHutangController::class, 'reject'])->name('reject');
        });
        
        // PROGRAM KERJA (Review)
        Route::prefix('proker')->name('proker.')->group(function () {
            Route::get('/', [BendaharaController::class, 'index'])->name('index');
            Route::get('/{programKerja}', [BendaharaController::class, 'show'])->name('show');
            Route::post('/{programKerja}/approve', [BendaharaController::class, 'approve'])->name('approve');
            Route::post('/{programKerja}/reject', [BendaharaController::class, 'reject'])->name('reject');
        });
    });
    
    // ========================================
    // ROUTE KETUA - SEMUA PAKE PREFIX JELAS
    // ========================================
    Route::prefix('ketua')->name('ketua.')->group(function () {
        
        // PENGAJUAN BUDGET
        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
            Route::get('/', [KetuaPengajuanController::class, 'index'])->name('index');
            Route::get('/{pengajuanBudget}', [KetuaPengajuanController::class, 'show'])->name('show');
            Route::post('/{pengajuanBudget}/approve', [KetuaPengajuanController::class, 'approve'])->name('approve');
            Route::post('/{pengajuanBudget}/reject', [KetuaPengajuanController::class, 'reject'])->name('reject');
        });
        
        // PENGAJUAN HUTANG
        Route::prefix('hutang')->name('hutang.')->group(function () {
            Route::get('/', [KetuaHutangController::class, 'index'])->name('index');
            Route::get('/{pengajuanHutang}', [KetuaHutangController::class, 'show'])->name('show');
            Route::post('/{pengajuanHutang}/approve', [KetuaHutangController::class, 'approve'])->name('approve');
            Route::post('/{pengajuanHutang}/reject', [KetuaHutangController::class, 'reject'])->name('reject');
        });
        
        // PROGRAM KERJA (Review)
        Route::prefix('proker')->name('proker.')->group(function () {
            Route::get('/', [KetuaController::class, 'index'])->name('index');
            Route::get('/{programKerja}', [KetuaController::class, 'show'])->name('show');
            Route::post('/{programKerja}/approve', [KetuaController::class, 'approve'])->name('approve');
            Route::post('/{programKerja}/reject', [KetuaController::class, 'reject'])->name('reject');
        });
    });
    
    // Route untuk Pencairan (Bendahara)
    Route::prefix('pencairan')->name('pencairan.')->group(function () {
        Route::get('/', [PencairanController::class, 'index'])->name('index');
        Route::post('/{type}/{id}/cairkan', [PencairanController::class, 'cairkan'])->name('cairkan');
    });

    // Route untuk History
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/program-kerja', [HistoryController::class, 'index'])->name('program-kerja');
        Route::get('/program-kerja/{programKerja}', [HistoryController::class, 'show'])->name('program-kerja.show');
        
        Route::get('/pengajuan-budget', [HistoryPengajuanController::class, 'index'])->name('pengajuan-budget');
        Route::get('/pengajuan-budget/{pengajuanBudget}', [HistoryPengajuanController::class, 'show'])->name('pengajuan-budget.show');
        
        Route::get('/hutang', [HistoryController::class, 'hutang'])->name('hutang');
    });

    // Route untuk Kas
    Route::prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/export', [KasController::class, 'export'])->name('export');
        Route::post('/setor', [KasController::class, 'setor'])->name('setor');
    });
});

require __DIR__.'/auth.php';
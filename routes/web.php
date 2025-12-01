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

    // Route untuk Bendahara - Konfirmasi
    Route::prefix('bendahara')->name('bendahara.')->group(function () {
        Route::get('/', [BendaharaController::class, 'index'])->name('index');
        Route::get('/{programKerja}', [BendaharaController::class, 'show'])->name('show');
        Route::post('/{programKerja}/approve', [BendaharaController::class, 'approve'])->name('approve');
        Route::post('/{programKerja}/reject', [BendaharaController::class, 'reject'])->name('reject');
    });
    
    // Route untuk Ketua - Approval
    Route::prefix('ketua')->name('ketua.')->group(function () {
        Route::get('/', [KetuaController::class, 'index'])->name('index');
        Route::get('/{programKerja}', [KetuaController::class, 'show'])->name('show');
        Route::post('/{programKerja}/approve', [KetuaController::class, 'approve'])->name('approve');
        Route::post('/{programKerja}/reject', [KetuaController::class, 'reject'])->name('reject');
    });

    // Route untuk Pencairan (Bendahara)
    Route::prefix('pencairan')->name('pencairan.')->group(function () {
        Route::get('/', [PencairanController::class, 'index'])->name('index');
        Route::get('/{programKerja}', [PencairanController::class, 'show'])->name('show');
        Route::post('/{programKerja}/cairkan', [PencairanController::class, 'cairkan'])->name('cairkan');
    });

    // Route untuk History
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/program-kerja', [HistoryController::class, 'index'])->name('program-kerja');
        Route::get('/program-kerja/{programKerja}', [HistoryController::class, 'show'])->name('program-kerja.show');
    });

    Route::prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [App\Http\Controllers\KasController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\KasController::class, 'export'])->name('export');
        Route::post('/setor', [App\Http\Controllers\KasController::class, 'setor'])->name('setor');
    });
    
});

require __DIR__.'/auth.php';
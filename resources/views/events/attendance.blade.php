@extends('layouts.app')

@section('title', 'Daftar Hadir - ' . $event->nama_event)

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Back Button & Title -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('events.index') }}" class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->nama_event }}</h1>
                <p class="text-gray-600 mt-1">{{ $event->waktu_pelaksanaan->format('d M Y') }} • {{ $event->tempat_pelaksanaan }}</p>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Hadir -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Hadir</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1" id="statTotalHadir">{{ $event->total_hadir }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Target Peserta -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Target</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">{{ $event->jumlah_peserta }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Persentase -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Persentase</p>
                        <p class="text-2xl font-bold text-green-900 mt-1" id="statPersen">{{ $event->persen_hadir }}%</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sisa Kuota -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Sisa Kuota</p>
                        <p class="text-2xl font-bold text-orange-900 mt-1" id="statSisaKuota">{{ $event->sisa_kuota }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Scan QR Code</h2>
            <button onclick="toggleScanner()" id="scannerToggle" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                <span id="scannerToggleText">Start Scanner</span>
            </button>
        </div>

        <!-- Scanner Container -->
        <div id="scannerContainer" class="hidden">
            <div class="bg-gray-900 rounded-lg p-4">
                <div id="reader" class="w-full"></div>
            </div>
            
            <!-- Scanner Status -->
            <div id="scannerStatus" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-blue-900">Scanner aktif. Arahkan QR Code ke kamera...</p>
                </div>
            </div>
        </div>

        <!-- Manual Input (Alternative) -->
        <div class="mt-4">
            <div class="flex items-center space-x-2">
                <div class="flex-1">
                    <input type="text" id="manualNik" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 transition"
                        placeholder="Atau masukkan NIK secara manual...">
                </div>
                <button onclick="manualSubmit()" class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Daftar Kehadiran</h2>
            <button onclick="refreshAttendance()" class="text-blue-600 hover:text-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bidang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="attendanceTableBody">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $attendance->nik }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $attendance->username }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                                    {{ $attendance->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $attendance->bidang }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->waktu_hadir->format('d M Y, H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="7" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada yang hadir</p>
                                <p class="text-gray-500 text-sm">Scan QR Code untuk mulai absensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($attendances->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
#reader {
    border: 2px solid #3b82f6;
    border-radius: 8px;
}

#reader video {
    border-radius: 8px;
}
</style>
@endpush

@push('scripts')
<!-- HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let isScanning = false;

function toggleScanner() {
    if (isScanning) {
        stopScanner();
    } else {
        startScanner();
    }
}

async function startScanner() {
    const scannerContainer = document.getElementById('scannerContainer');
    const scannerStatus = document.getElementById('scannerStatus');
    const toggleBtn = document.getElementById('scannerToggle');
    const toggleText = document.getElementById('scannerToggleText');

    scannerContainer.classList.remove('hidden');
    scannerStatus.classList.remove('hidden');
    toggleBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
    toggleText.textContent = 'Stop Scanner';

    html5QrCode = new Html5Qrcode("reader");

    try {
        await html5QrCode.start(
            { facingMode: "environment" }, // Use back camera
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        );
        isScanning = true;
    } catch (err) {
        console.error('Scanner error:', err);
        Swal.fire('Error!', 'Gagal memulai scanner. Pastikan kamera diizinkan.', 'error');
        stopScanner();
    }
}

async function stopScanner() {
    if (html5QrCode) {
        try {
            await html5QrCode.stop();
        } catch (err) {
            console.error('Stop scanner error:', err);
        }
    }

    const scannerContainer = document.getElementById('scannerContainer');
    const scannerStatus = document.getElementById('scannerStatus');
    const toggleBtn = document.getElementById('scannerToggle');
    const toggleText = document.getElementById('scannerToggleText');

    scannerContainer.classList.add('hidden');
    scannerStatus.classList.add('hidden');
    toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
    toggleBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    toggleText.textContent = 'Start Scanner';
    isScanning = false;
}

function onScanSuccess(decodedText, decodedResult) {
    // Stop scanner temporarily to prevent multiple scans
    stopScanner();
    
    // Process the scanned NIK - dengan parameter fromScan = true
    processAttendance(decodedText, true);
}

function onScanFailure(error) {
    // Silent - don't show errors for scan failures
}

// ✅ TAMBAHKAN PARAMETER fromScan untuk membedakan dari scan atau manual
async function processAttendance(nik, fromScan = false) {
    try {
        const response = await fetch(`/events/{{ $event->id }}/scan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nik: nik })
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: `<strong>${data.data.name}</strong><br>Kehadiran berhasil dicatat!`,
                timer: 2000,
                showConfirmButton: false
            });

            // Update statistics
            updateStatistics(data.statistics);

            // Refresh attendance list
            await refreshAttendance();

            // ✅ HANYA restart scanner jika dari scan, BUKAN dari manual input
            if (fromScan) {
                setTimeout(() => {
                    startScanner();
                }, 2000);
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
            });

            // ✅ HANYA restart scanner jika dari scan
            if (fromScan) {
                setTimeout(() => {
                    startScanner();
                }, 1500);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');

        // ✅ HANYA restart scanner jika dari scan
        if (fromScan) {
            setTimeout(() => {
                startScanner();
            }, 1500);
        }
    }
}

// ✅ Manual submit TIDAK mengirim parameter fromScan (default = false)
function manualSubmit() {
    const nik = document.getElementById('manualNik').value.trim();
    
    if (!nik) {
        Swal.fire('Error!', 'NIK tidak boleh kosong.', 'error');
        return;
    }

    processAttendance(nik); // fromScan = false (default)
    document.getElementById('manualNik').value = '';
}

async function refreshAttendance() {
    try {
        const response = await fetch(`/events/{{ $event->id }}/list`);
        const data = await response.json();

        if (data.success) {
            updateStatistics(data.statistics);
            updateAttendanceTable(data.data);
        }
    } catch (error) {
        console.error('Refresh error:', error);
    }
}

function updateStatistics(stats) {
    document.getElementById('statTotalHadir').textContent = stats.total_hadir;
    document.getElementById('statPersen').textContent = stats.persen_hadir + '%';
    document.getElementById('statSisaKuota').textContent = stats.sisa_kuota;
}

function updateAttendanceTable(attendances) {
    const tbody = document.getElementById('attendanceTableBody');
    const emptyState = document.getElementById('emptyState');

    if (attendances.length === 0) {
        if (!emptyState) {
            tbody.innerHTML = `
                <tr id="emptyState">
                    <td colspan="7" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-600 font-semibold">Belum ada yang hadir</p>
                        <p class="text-gray-500 text-sm">Scan QR Code untuk mulai absensi</p>
                    </td>
                </tr>
            `;
        }
        return;
    }

    tbody.innerHTML = attendances.map((att, index) => `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${att.nik}</td>
            <td class="px-6 py-4 text-sm text-gray-900">${att.name}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${att.username}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black text-white">
                    ${att.role}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">${att.bidang}</td>
            <td class="px-6 py-4 text-sm text-gray-500">${att.waktu_hadir}</td>
        </tr>
    `).join('');
}

// Auto refresh every 30 seconds
setInterval(refreshAttendance, 30000);

// Enter key for manual input
document.getElementById('manualNik').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        manualSubmit();
    }
});
</script>
@endpush
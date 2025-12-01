<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas Global - {{ $periodLabel }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body class="bg-white p-8">
    
    <!-- Print Button (hidden when printing) -->
    <div class="no-print mb-6 flex justify-end gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
            Print / Save PDF
        </button>
        <button onclick="window.close()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
            Tutup
        </button>
    </div>

    <!-- Report Header -->
    <div class="border-b-2 border-gray-800 pb-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">LAPORAN KAS GLOBAL</h1>
                <p class="text-lg text-gray-600 mt-2">Periode: {{ $periodLabel }}</p>
            </div>
            <div class="text-right">
                <img src="{{ asset('images/logostep.png') }}" alt="Logo" class="h-16 mb-2">
                <p class="text-sm text-gray-600">Gudang MDD</p>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-gray-600 font-semibold">Saldo Awal Periode</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">
                Rp {{ number_format($saldoAwal, 0, ',', '.') }}
            </p>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-gray-600 font-semibold">Total Kas Masuk</p>
            <p class="text-2xl font-bold text-green-700 mt-1">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </p>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-gray-600 font-semibold">Total Kas Keluar</p>
            <p class="text-2xl font-bold text-red-700 mt-1">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="bg-gray-800 text-white rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-80">Saldo Akhir Periode</p>
                <p class="text-3xl font-bold mt-1">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-80">Total Transaksi</p>
                <p class="text-2xl font-bold mt-1">{{ $totalTransaksi }}</p>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Rincian Transaksi</h2>
        
        @if($histories->isEmpty())
            <div class="border border-gray-200 rounded-lg p-12 text-center">
                <p class="text-gray-500 font-semibold">Tidak ada transaksi pada periode ini</p>
            </div>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">No</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">Status</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">Sumber</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">Keterangan</th>
                        <th class="border border-gray-300 px-3 py-2 text-right text-xs font-bold">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2 text-right text-xs font-bold">Saldo</th>
                        <th class="border border-gray-300 px-3 py-2 text-left text-xs font-bold">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $index => $history)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="border border-gray-300 px-3 py-2 text-xs">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-xs whitespace-nowrap">
                            {{ $history->tanggal_transaksi->format('d M Y H:i') }}
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">
                            @if($history->jenis === 'masuk')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Masuk</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Keluar</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">{{ ucfirst($history->sumber) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">{{ $history->keterangan }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-xs text-right font-semibold {{ $history->jenis === 'masuk' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $history->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($history->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs text-right font-semibold">
                            Rp {{ number_format($history->saldo_sesudah, 0, ',', '.') }}
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-xs">{{ $history->dilakukanOleh->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Report Footer -->
    <div class="border-t-2 border-gray-300 pt-6 mt-8">
        <div class="flex justify-between items-end">
            <div class="text-sm text-gray-600">
                <p>Dicetak oleh: <strong>{{ Auth::user()->name }}</strong></p>
                <p>Tanggal Cetak: <strong>{{ now()->format('d M Y H:i') }}</strong></p>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-16">Mengetahui,</p>
                <p class="text-sm font-bold border-t border-gray-800 pt-2 inline-block px-8">
                    {{ Auth::user()->role->nama === 'bendahara' ? 'Bendahara' : 'Pimpinan' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Auto print on load -->
    <script>
        window.onload = function() {
            // Auto print after 500ms (give time for page to render)
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
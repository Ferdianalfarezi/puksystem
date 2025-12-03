<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class EventAttendanceController extends Controller
{
    /**
     * Display attendance page for specific event
     */
    public function index(Event $event): View
    {
        $event->load(['creator', 'attendances.user']);
        
        $attendances = $event->attendances()
            ->orderBy('waktu_hadir', 'desc')
            ->paginate(20);
        
        return view('events.attendance', compact('event', 'attendances'));
    }

    /**
     * Store attendance via QR scan
     */
    public function scan(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string'],
        ]);

        try {
            // Find user by NIK
            $user = User::where('nik', $validated['nik'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK tidak ditemukan dalam sistem.'
                ], 404);
            }

            // Check if user is active
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak aktif. Tidak dapat melakukan absensi.',
                    'user' => [
                        'name' => $user->name,
                        'nik' => $user->nik,
                    ]
                ], 422);
            }

            // Check if user already attended
            if ($event->isUserAttended($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User sudah melakukan absensi untuk event ini.',
                    'user' => [
                        'name' => $user->name,
                        'nik' => $user->nik,
                    ]
                ], 422);
            }

            // Check if event is full
            if ($event->total_hadir >= $event->jumlah_peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event sudah penuh. Kuota peserta habis.',
                ], 422);
            }

            // Create attendance record
            $attendance = EventAttendance::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'nik' => $user->nik,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role->nama ?? 'No Role',
                'bidang' => $user->bidang->nama ?? 'No Bidang',
                'waktu_hadir' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran berhasil dicatat!',
                'data' => [
                    'id' => $attendance->id,
                    'name' => $attendance->name,
                    'nik' => $attendance->nik,
                    'username' => $attendance->username,
                    'role' => $attendance->role,
                    'bidang' => $attendance->bidang,
                    'waktu_hadir' => $attendance->waktu_hadir->format('d M Y, H:i:s'),
                ],
                'statistics' => [
                    'total_hadir' => $event->total_hadir,
                    'jumlah_peserta' => $event->jumlah_peserta,
                    'persen_hadir' => $event->persen_hadir,
                    'sisa_kuota' => $event->sisa_kuota,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance list (for real-time update)
     */
    public function list(Event $event): JsonResponse
    {
        $attendances = $event->attendances()
            ->orderBy('waktu_hadir', 'desc')
            ->get()
            ->map(function($attendance) {
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->name,
                    'nik' => $attendance->nik,
                    'username' => $attendance->username,
                    'role' => $attendance->role,
                    'bidang' => $attendance->bidang,
                    'waktu_hadir' => $attendance->waktu_hadir->format('d M Y, H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attendances,
            'statistics' => [
                'total_hadir' => $event->total_hadir,
                'jumlah_peserta' => $event->jumlah_peserta,
                'persen_hadir' => $event->persen_hadir,
                'sisa_kuota' => $event->sisa_kuota,
            ]
        ]);
    }

    /**
     * Export attendance to Excel (optional feature)
     */
    public function export(Event $event)
    {
        // TODO: Implement Excel export jika diperlukan
        // Bisa pakai Laravel Excel package
        
        return response()->json([
            'success' => false,
            'message' => 'Export feature belum diimplementasikan.'
        ], 501);
    }
}
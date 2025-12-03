<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $events = Event::with('creator')->latest()->paginate(20);
        
        return view('events.index', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:255'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'waktu_pelaksanaan' => ['required', 'date'],
            'tempat_pelaksanaan' => ['required', 'string', 'max:255'],
        ], [
            'nama_event.required' => 'Nama event wajib diisi.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang.',
            'waktu_pelaksanaan.required' => 'Waktu pelaksanaan wajib diisi.',
            'tempat_pelaksanaan.required' => 'Tempat pelaksanaan wajib diisi.',
        ]);

        try {
            Event::create([
                'nama_event' => $validated['nama_event'],
                'jumlah_peserta' => $validated['jumlah_peserta'],
                'waktu_pelaksanaan' => $validated['waktu_pelaksanaan'],
                'tempat_pelaksanaan' => $validated['tempat_pelaksanaan'],
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan event.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load('creator');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'jumlah_peserta' => $event->jumlah_peserta,
                'waktu_pelaksanaan' => $event->waktu_pelaksanaan->format('Y-m-d'),
                'tempat_pelaksanaan' => $event->tempat_pelaksanaan,
                'created_by' => $event->created_by,
                'created_by_name' => $event->creator->name,
                'total_hadir' => $event->total_hadir,
                'persen_hadir' => $event->persen_hadir,
                'sisa_kuota' => $event->sisa_kuota,
                'status_event' => $event->status_event,
                'created_at' => $event->created_at->format('d M Y, H:i'),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:255'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'waktu_pelaksanaan' => ['required', 'date'],
            'tempat_pelaksanaan' => ['required', 'string', 'max:255'],
        ], [
            'nama_event.required' => 'Nama event wajib diisi.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang.',
            'waktu_pelaksanaan.required' => 'Waktu pelaksanaan wajib diisi.',
            'tempat_pelaksanaan.required' => 'Tempat pelaksanaan wajib diisi.',
        ]);

        try {
            $event->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui event.',
                'errors' => ['general' => ['Terjadi kesalahan sistem.']]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        try {
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus event.'
            ], 500);
        }
    }
}
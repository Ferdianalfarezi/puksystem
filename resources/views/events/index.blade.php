@extends('layouts.app')

@section('title', 'Events Management')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="space-y-4">
        <!-- Title & Buttons -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events Management</h1>
                <p class="text-gray-600 mt-1">Manage events and attendance tracking</p>
            </div>
            <div class="flex space-x-3">
                <!-- Button Add Event -->
                <button 
                    onclick="openCreateModal()"
                    class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add Event</span>
                </button>
            </div>
        </div>

        <!-- Statistics Badges -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Events -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Events</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $events->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Upcoming</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">{{ $events->where('status_event', 'upcoming')->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Ongoing Events -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Ongoing</p>
                        <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $events->where('status_event', 'ongoing')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Finished Events -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Finished</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $events->where('status_event', 'finished')->count() }}</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="w-full md:w-1/2 lg:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                id="searchInput"
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                placeholder="Search events..."
                onkeyup="searchTable()"
            >
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="eventsTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Event</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tempat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pembuat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="eventsTableBody">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $event->nama_event }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $event->waktu_pelaksanaan->format('d M Y') }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">{{ $event->tempat_pelaksanaan }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold text-gray-900">{{ $event->total_hadir }} / {{ $event->jumlah_peserta }}</span>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $event->persen_hadir }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $event->persen_hadir }}%</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">{{ $event->creator->name }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($event->status_event === 'upcoming')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Upcoming
                                    </span>
                                @elseif($event->status_event === 'ongoing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Ongoing
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Finished
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Daftar Hadir -->
                                    <a href="{{ route('events.attendance.index', $event->id) }}"
                                    class="bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600 transition"
                                    title="Daftar Hadir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </a>
                                    
                                    <!-- Edit -->
                                    <button onclick="openEditModal({{ $event->id }})"
                                            class="bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 transition"
                                            title="Edit Event">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <button onclick="deleteEvent({{ $event->id }})"
                                            class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition"
                                            title="Delete Event">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No events found</p>
                                <p class="text-gray-500 text-sm">Click "Add Event" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination -->
        @if($events->hasPages())
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Include Create Modal -->
@include('events.create')

<!-- Include Edit Modal -->
@include('events.edit')

@endsection

@push('scripts')
<script>
    // Search functionality
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const tableBody = document.getElementById('eventsTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(searchInput)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    }

    function openCreateModal() {
        const modal = document.getElementById('createModal');
        
        document.getElementById('createForm').reset();
        clearErrors();
        
        document.body.style.overflow = 'hidden';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        void modal.offsetWidth;
        
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });
    }

    async function openEditModal(id) {
        try {
            const response = await fetch(`/events/${id}`);
            const data = await response.json();
            
            if (data.success) {
                const event = data.data;
                document.getElementById('editEventId').value = event.id;
                document.getElementById('editNamaEvent').value = event.nama_event;
                document.getElementById('editJumlahPeserta').value = event.jumlah_peserta;
                document.getElementById('editWaktuPelaksanaan').value = event.waktu_pelaksanaan;
                document.getElementById('editTempatPelaksanaan').value = event.tempat_pelaksanaan;
                
                clearErrors();
                
                const modal = document.getElementById('editModal');
                document.body.style.overflow = 'hidden';
                
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                void modal.offsetWidth;
                
                requestAnimationFrame(() => {
                    modal.classList.add('active');
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load event data', 'error');
        }
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('active');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('createForm').reset();
            clearErrors();
            document.body.style.overflow = '';
        }, 250);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('active');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            clearErrors();
            document.body.style.overflow = '';
        }, 250);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Submit Create Form
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch('/events', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeCreateModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-create-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    // Submit Edit Form
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editEventId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/events/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeEditModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-edit-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    async function deleteEvent(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "Event ini akan dihapus beserta data kehadirannya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/events/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete event!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush
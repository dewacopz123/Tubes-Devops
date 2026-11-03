@extends('layouts.app', ['sidebar' => 'admin'])

@section('title', 'Manajemen Antrian')

@section('content')
<div x-data="{ 
    activeFilter: 'ALL', 
    countdown: 30, 
    paused: false, 
    loading: false,
    showConfirmModal: false, 
    modalTitle: '', 
    modalMessage: '', 
    modalFormId: '',
    openModal(title, msg, formId) {
        this.modalTitle = title;
        this.modalMessage = msg;
        this.modalFormId = formId;
        this.showConfirmModal = true;
    }
}" 
x-init="
    setInterval(() => {
        if (!paused && !showConfirmModal) {
            countdown--;
            if (countdown <= 0) {
                window.location.reload();
            }
        }
    }, 1000)
"
class="space-y-6">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.petugas')],
        ['label' => 'Antrian', 'url' => null]
    ]" />

    {{-- Header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Antrian</h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar antrian hari ini
                @if($tanggal) <span class="font-medium text-gray-900">({{ $tanggal }})</span> @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="window.location.reload();" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2 flex items-center gap-1.5">
                <span class="icon-[tabler--refresh] w-4 h-4"></span>
                Refresh
            </button>
        </div>
    </div>

    {{-- Auto Refresh & Filters --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        {{-- Progress / Countdown --}}
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Refresh otomatis:</span>
            <div class="w-32 bg-gray-200 rounded-full h-2.5">
                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-1000" :style="'width: ' + (countdown / 30 * 100) + '%'"></div>
            </div>
            <span class="text-sm font-semibold text-gray-900" x-text="countdown + 's'"></span>
            <button type="button" @click="paused = !paused" class="text-gray-500 hover:text-indigo-600 focus:outline-none flex items-center justify-center">
                <span x-show="!paused" class="icon-[tabler--pause] w-4 h-4"></span>
                <span x-show="paused" class="icon-[tabler--play] w-4 h-4" style="display: none;"></span>
            </button>
        </div>

        {{-- Filters (Flowbite Pills style) --}}
        <div class="flex flex-wrap gap-2">
            <button @click="activeFilter = 'ALL'" :class="activeFilter === 'ALL' ? 'text-white bg-indigo-600 border border-indigo-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                Semua
            </button>
            <button @click="activeFilter = 'MENUNGGU'" :class="activeFilter === 'MENUNGGU' ? 'text-white bg-indigo-600 border border-indigo-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                Menunggu
            </button>
            <button @click="activeFilter = 'DILAYANI'" :class="activeFilter === 'DILAYANI' ? 'text-white bg-indigo-600 border border-indigo-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                Dilayani
            </button>
            <button @click="activeFilter = 'SELESAI'" :class="activeFilter === 'SELESAI' ? 'text-white bg-indigo-600 border border-indigo-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                Selesai
            </button>
            <button @click="activeFilter = 'DIBATALKAN'" :class="activeFilter === 'DIBATALKAN' ? 'text-white bg-indigo-600 border border-indigo-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors">
                Dibatalkan
            </button>
        </div>
    </div>

    {{-- Flowbite Alerts --}}
    @if(session('success'))
        <div class="flex items-center p-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
            <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
            <span class="sr-only">Success</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
            <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
            <span class="sr-only">Error</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Flowbite Table --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
        {{-- Loading Skeleton --}}<div x-show="loading" class="space-y-3">
            @for($i = 0; $i < 5; $i++)
            <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
            @endfor
        </div>
        
        <table class="w-full text-sm text-center text-gray-500" x-show="!loading">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3">No Antrian</th>
                    <th scope="col" class="px-6 py-3">No Lab</th>
                    <th scope="col" class="px-6 py-3 text-left">Pasien</th>
                    <th scope="col" class="px-6 py-3">Jadwal</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($queues as $i => $q)
                    @php
                        $status = strtoupper($q['status'] ?? '-');

                        $badgeClass = 'bg-gray-100 text-gray-800';
                        if ($status === 'MENUNGGU') $badgeClass = 'bg-yellow-100 text-yellow-800';
                        if ($status === 'DILAYANI') $badgeClass = 'bg-blue-100 text-blue-800';
                        if ($status === 'SELESAI') $badgeClass = 'bg-green-100 text-green-800';
                        if ($status === 'DIBATALKAN') $badgeClass = 'bg-red-100 text-red-800';

                        $isFinal = in_array($status, ['SELESAI', 'DIBATALKAN'], true);
                        $isDilayani = $status === 'DILAYANI';
                    @endphp

                    <tr x-show="activeFilter === 'ALL' || activeFilter === '{{ $status }}'" 
                        :class="activeFilter === 'ALL' || activeFilter === '{{ $status }}' ? '' : 'hidden'"
                        class="border-b transition-colors border-l-4 {{ $isDilayani ? 'bg-indigo-50/40 hover:bg-indigo-50 border-l-indigo-600' : 'bg-white hover:bg-gray-50 border-l-transparent' }}">
                        <th scope="row" class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                            {{ $q['no_antrian'] ?? '-' }}
                        </th>
                        <td class="px-6 py-4 font-medium">{{ $q['no_lab'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-left">
                            <div class="font-semibold text-gray-900">{{ $q['nama'] ?? '-' }}</div>
                            <div class="text-xs text-gray-500">NIK: {{ $q['nik'] ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ formatDateTime($q['jadwal_pemeriksaan_at'] ?? null) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2 justify-center">
                                {{-- CALL --}}
                                <form id="call-form-{{ $q['id'] }}" method="POST" action="{{ route('antrian.call', $q['id']) }}">
                                    @csrf
                                    <button type="button" @click="openModal('Panggil Antrian', 'Panggil nomor antrian {{ $q['no_antrian'] }} ({{ $q['nama'] }})?', 'call-form-{{ $q['id'] }}')" class="font-medium text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 rounded-lg text-xs px-3 py-1.5 transition-opacity {{ $isFinal ? 'opacity-40 cursor-not-allowed' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                                        Call
                                    </button>
                                </form>

                                {{-- NEXT --}}
                                <form id="next-form-{{ $q['id'] }}" method="POST" action="{{ route('antrian.next', $q['id']) }}">
                                    @csrf
                                    <button type="button" @click="openModal('Selesaikan & Lanjut', 'Selesaikan pemeriksaan untuk {{ $q['nama'] }} dan panggil antrian berikutnya?', 'next-form-{{ $q['id'] }}')" class="font-medium text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 rounded-lg text-xs px-3 py-1.5 transition-opacity {{ $isFinal ? 'opacity-40 cursor-not-allowed' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                                        Next
                                    </button>
                                </form>

                                {{-- CANCEL --}}
                                <form id="cancel-form-{{ $q['id'] }}" method="POST" action="{{ route('antrian.cancel', $q['id']) }}">
                                    @csrf
                                    <button type="button" @click="openModal('Batalkan Antrian', 'Apakah Anda yakin ingin membatalkan antrian {{ $q['no_antrian'] }} ({{ $q['nama'] }})?', 'cancel-form-{{ $q['id'] }}')" class="font-medium text-red-700 bg-white border border-red-300 hover:bg-red-50 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-lg text-xs px-3 py-1.5 transition-opacity {{ $isFinal ? 'opacity-40 cursor-not-allowed' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <p class="text-sm font-semibold text-gray-700">Tidak ada antrian hari ini</p>
                            <p class="text-xs text-gray-500 mt-1">Data akan muncul ketika ada pendaftaran masuk.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Standardized Empty State --}}<div x-show="!loading && (queues ?? []).length === 0" class="flex flex-col items-center justify-center py-16">
            <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <span class="icon-[tabler--users] w-8 h-8 text-gray-400"></span>
            </div>
            <p class="text-sm font-semibold text-gray-700 mb-1">Belum ada antrian</p>
            <p class="text-xs text-gray-500">Data antrian akan muncul setelah pasien melakukan pendaftaran</p>
        </div>
    </div>

    {{-- Unified Confirmation Modal --}}
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" 
         style="display: none;">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                    <button type="button" @click="showConfirmModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <span class="icon-[tabler--x] w-5 h-5"></span>
                    </button>
                </div>
                
                {{-- Modal Body --}}
                <div class="p-4 md:p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="icon-[tabler--info-circle] w-6 h-6 text-indigo-600"></span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700" x-text="modalMessage"></p>
                        </div>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="flex items-center gap-2 p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button type="button" @click="showConfirmModal = false" class="flex-1 text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Batal
                    </button>
                    <button type="button" @click="paused = true; $el.disabled = true; document.getElementById(modalFormId).submit(); showConfirmModal = false;" class="flex-1 text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

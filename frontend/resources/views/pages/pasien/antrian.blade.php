@extends('layouts.app', ['sidebar' => 'pasien'])

@section('title', 'Antrian Pasien')

@section('content')
@php
    // Find patient's own queue
    $myQueue = null;
    $myQueueIndex = -1;
    foreach($queues as $idx => $q) {
        if (!empty($myPasienId) && ($q['pasien_id'] ?? null) == $myPasienId) {
            $myQueue = $q;
            $myQueueIndex = $idx;
            break;
        }
    }
    $myQueueStatus = $myQueue ? strtoupper($myQueue['status'] ?? '') : '';
    
    // Calculate waiting time estimate (in minutes)
    $waitingTimeEstimate = 0;
    if ($myQueue && $myQueueStatus === 'MENUNGGU') {
        $myNoAntrian = (int)($myQueue['no_antrian'] ?? 0);
        $currentNoAntrian = (int)($current['no_antrian'] ?? 0);
        
        if ($currentNoAntrian > 0 && $myNoAntrian > $currentNoAntrian) {
            $queueDiff = $myNoAntrian - $currentNoAntrian;
            $waitingTimeEstimate = $queueDiff * 15; // 15 minutes per queue
        } elseif ($currentNoAntrian == 0) {
            // No one being served yet, estimate based on position
            $waitingTimeEstimate = ($myQueueIndex + 1) * 15;
        }
    }
@endphp

<div x-data="{ 
    countdown: 15, 
    paused: false 
}" 
x-init="
    // Check if status changed for toast
    const prevStatus = localStorage.getItem('my_queue_status');
    const currentStatus = '{{ $myQueueStatus }}';
    if (prevStatus && currentStatus && prevStatus !== currentStatus && currentStatus !== '') {
        showToast('Status antrian Anda berubah: ' + currentStatus);
    }
    if (currentStatus) {
        localStorage.setItem('my_queue_status', currentStatus);
    }

    setInterval(() => {
        if (!paused) {
            countdown--;
            if (countdown <= 0) {
                window.location.reload();
            }
        }
    }, 1000)
"
class="max-w-6xl mx-auto">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pasien.dashboard')],
        ['label' => 'Antrian', 'url' => null]
    ]" />

    {{-- Header with Tabs --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('assets/images/logo/Logo.png') }}" class="h-12">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Pendaftaran Pemeriksaan Laboratorium
                </h1>
                <p class="text-sm text-gray-500">
                    Sistem Pendaftaran Tes Lab Online
                </p>
            </div>
        </div>

        {{-- Flowbite Tabs --}}
        <div class="inline-flex rounded-lg shadow-sm" role="group">
            <a href="{{ url('/pendaftaran') }}" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700">
                Pendaftaran
            </a>
            <a href="{{ url('/antrian') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-e-lg hover:bg-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700">
                Antrian
            </a>
        </div>
    </div>

    {{-- Auto Refresh Progress Bar --}}
    <div class="flex items-center gap-3 p-3 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <span class="text-sm font-medium text-gray-700">Refresh otomatis:</span>
        <div class="flex-1 bg-gray-200 rounded-full h-2">
            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-1000" :style="'width: ' + (countdown / 15 * 100) + '%'"></div>
        </div>
        <span class="text-sm font-semibold text-gray-900" x-text="countdown + 's'"></span>
        <button type="button" @click="paused = !paused" class="text-gray-500 hover:text-indigo-600 focus:outline-none">
            <span x-show="!paused" class="icon-[tabler--pause] w-4 h-4"></span>
            <span x-show="paused" class="icon-[tabler--play] w-4 h-4" style="display: none;"></span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        {{-- Current Queue Card --}}
        <div class="block p-6 bg-indigo-600 rounded-lg shadow">
            <div class="flex items-center justify-center mb-2">
                <span class="bg-white text-indigo-600 text-xs font-semibold px-3 py-1 rounded-full">
                    Live
                </span>
            </div>
            <h5 class="text-4xl font-bold text-center text-white mb-2">
                {{ $current['no_antrian'] ?? '-' }}
            </h5>
            <p class="text-sm text-center text-white">Nomor Antrian Saat Ini</p>
        </div>

        {{-- Waiting Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['menunggu'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Pasien Menunggu</p>
        </div>

        {{-- Completed Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['selesai'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Sudah Dilayani</p>
        </div>

        {{-- Total Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['total'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Total Antrian</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Currently Being Served Card --}}
        <div class="p-6 bg-white border-2 border-indigo-300 rounded-lg shadow">
            <h5 class="mb-4 text-lg font-bold text-gray-900">Sedang Dilayani</h5>

            @if($current)
                <div class="p-4 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-lg">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">No Antrian</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ $current['no_antrian'] ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">No Lab</p>
                            <p class="font-semibold text-gray-900">
                                {{ $current['no_lab'] ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Jadwal</p>
                            <p class="text-sm text-gray-900">
                                {{ formatDateTime($current['jadwal_pemeriksaan_at'] ?? null) }}
                            </p>
                        </div>
                    </div>

                    <span class="bg-indigo-600 text-white text-xs font-medium px-3 py-1 rounded-full">
                        DILAYANI
                    </span>
                </div>
            @else
                <div class="flex items-center justify-center h-32 text-gray-400">
                    Belum ada pasien
                </div>
            @endif
        </div>

        {{-- Queue List --}}
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h5 class="text-lg font-bold text-gray-900">
                        Daftar Antrian Hari Ini
                    </h5>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="queue-date">{{ $tanggal ?? '-' }}</span>
                        <span class="mx-1">•</span>
                        <span class="font-medium text-indigo-600">
                            <span id="queue-time">00:00:00</span> WIB
                        </span>
                    </p>
                </div>

                <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-lg">
                    Total: {{ $stats['total'] ?? 0 }}
                </span>
            </div>

            @if(!empty($queues))
                <div class="space-y-3">
                    @foreach($queues as $q)
                        @php
                            $status = strtoupper($q['status'] ?? '-');
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            if ($status === 'MENUNGGU' || $status === 'PENDING')
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                            elseif ($status === 'DILAYANI' || $status === 'PROSES')
                                $badgeClass = 'bg-blue-100 text-blue-800';
                            elseif ($status === 'SELESAI' || $status === 'DONE')
                                $badgeClass = 'bg-green-100 text-green-800';
                            elseif ($status === 'DIBATALKAN' || $status === 'BATAL' || $status === 'CANCEL')
                                $badgeClass = 'bg-red-100 text-red-800';
                            
                            $isMyQueue = !empty($myPasienId) && ($q['pasien_id'] ?? null) == $myPasienId;
                        @endphp

                        <div class="flex items-start justify-between gap-4 p-4 border rounded-lg transition-all {{ $isMyQueue ? 'border-indigo-500 bg-indigo-50/50 shadow-md' : 'border-gray-200 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg {{ $isMyQueue ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600' }}">
                                    <span class="font-bold">{{ $q['no_antrian'] ?? '-' }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-semibold text-gray-900">
                                            Ruangan: {{ $q['no_lab'] ?? '-' }}
                                        </p>
                                        @if($isMyQueue)
                                            <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                                Antrian Anda
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Jadwal: {{ formatDateTime($q['jadwal_pemeriksaan_at'] ?? null) }}
                                    </p>
                                    @if($isMyQueue && $waitingTimeEstimate > 0 && $status === 'MENUNGGU')
                                        <p class="text-xs text-indigo-600 font-medium mt-1">
                                            <span class="icon-[tabler--clock] inline w-3 h-3 mr-1"></span>
                                            Estimasi tunggu: ~{{ $waitingTimeEstimate }} menit
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center py-12 text-gray-400">
                    Belum ada pendaftaran hari ini
                </div>
            @endif
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden max-w-xs p-4 text-gray-900 bg-white rounded-lg shadow-lg border-2 border-indigo-200" role="alert">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="icon-[tabler--bell] w-5 h-5 text-indigo-600"></span>
            </div>
            <div class="text-sm font-medium" id="toast-message"></div>
        </div>
    </div>

</div>

<script>
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

        const dateEl = document.getElementById('queue-date');
        if (dateEl && (dateEl.textContent || '').trim() === '-') {
            dateEl.textContent = now.toLocaleDateString('id-ID', dateOptions);
        }

        document.getElementById('queue-time').textContent =
            now.toLocaleTimeString('id-ID', timeOptions);
    }

    function showToast(message) {
        const toast = document.getElementById('toast-notification');
        const toastMsg = document.getElementById('toast-message');
        if (toast && toastMsg) {
            toastMsg.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>

@endsection
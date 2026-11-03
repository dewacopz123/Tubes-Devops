@extends('layouts.app', ['sidebar' => 'dokter'])

@section('title', 'Dashboard Dokter')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.dokter')],
        ['label' => 'Dashboard', 'url' => null]
    ]" />

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('assets/images/logo/Logo.png') }}" class="h-12">
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Dashboard Dokter
                </h1>
                <p class="text-sm text-gray-500">
                    Monitoring Antrian & Pemeriksaan Pasien
                </p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Current Queue Card --}}
        <div class="block p-6 bg-indigo-600 rounded-lg shadow">
            <div class="flex items-center justify-center mb-2">
                <span class="bg-white text-primary-600 text-xs font-semibold px-3 py-1 rounded-full">
                    Aktif
                </span>
            </div>
            <h5 class="text-4xl font-bold text-center text-white mb-2">
                {{ $current['no_antrian'] ?? '-' }}
            </h5>
            <p class="text-sm text-center text-white">Antrian Saat Ini</p>
        </div>

        {{-- Served Today Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['dilayani'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Pasien Dilayani Hari Ini</p>
        </div>

        {{-- Waiting Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['menunggu'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Pasien Menunggu</p>
        </div>

        {{-- Total Card --}}
        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h5 class="text-3xl font-bold text-center text-gray-900 mb-2">
                {{ $stats['total'] ?? 0 }}
            </h5>
            <p class="text-sm text-center text-gray-500">Total Pemeriksaan</p>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Current Examination Card --}}
        <div class="p-6 bg-white border-2 border-primary-300 rounded-lg shadow">
            <h5 class="mb-4 text-lg font-bold text-gray-900">Pemeriksaan Saat Ini</h5>

            @if($current)
                <div class="p-4 bg-gradient-to-br from-primary-50 to-white border border-primary-100 rounded-lg">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">No Antrian</p>
                            <p class="text-2xl font-bold text-primary-600">
                                {{ $current['no_antrian'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Ruangan</p>
                            <p class="font-semibold text-gray-900">
                                {{ $current['no_lab'] }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs text-gray-500">Jadwal</p>
                        <p class="text-sm text-gray-900">
                            {{ formatDateTime($current['jadwal_pemeriksaan_at'] ?? null) }}
                        </p>
                    </div>

                    <span class="bg-indigo-600 text-white text-xs font-medium px-3 py-1 rounded-full">
                        {{ $current['status'] }}
                    </span>
                </div>
            @else
                <div class="flex items-center justify-center h-32 text-gray-400">
                    Tidak ada pemeriksaan aktif
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
                        <span class="font-medium text-primary-600">
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

                            if ($status === 'MENUNGGU') $badgeClass = 'bg-yellow-100 text-yellow-800';
                            elseif ($status === 'DILAYANI') $badgeClass = 'bg-blue-100 text-blue-800';
                            elseif ($status === 'SELESAI') $badgeClass = 'bg-green-100 text-green-800';
                        @endphp

                        <div class="flex items-start justify-between gap-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 bg-indigo-50 rounded-lg">
                                    <span class="font-bold text-primary-600">{{ $q['no_antrian'] }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        Ruangan {{ $q['no_lab'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Jadwal: {{ formatDateTime($q['jadwal_pemeriksaan_at'] ?? null) }}
                                    </p>
                                </div>
                            </div>

                            <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center py-12 text-gray-400">
                    Belum ada antrian hari ini
                </div>
            @endif

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

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>

</div>
@endsection
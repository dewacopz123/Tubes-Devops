@extends('layouts.app', ['sidebar' => 'admin'])

@section('title', 'Dashboard Petugas')

@section('content')
<div class="space-y-8">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.petugas')],
        ['label' => 'Dashboard', 'url' => null]
    ]" />

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Dashboard Petugas</h2>
        <p class="text-sm text-gray-500 mt-1">Ringkasan antrian hari ini & riwayat perubahan data.</p>
    </div>

    {{-- SECTION 1: Queue Summary + Realtime Clock --}}
    <section class="space-y-4" x-data="realtimeClock()" x-init="start()">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Antrian Hari Ini</h3>
                <p class="text-sm text-gray-500">Update otomatis sesuai data sistem.</p>
            </div>

            {{-- Realtime Clock --}}
            <div class="inline-flex items-center gap-2 p-4 bg-white border border-gray-200 rounded-lg shadow">
                <span class="text-xs font-medium text-gray-500">Waktu</span>
                <span class="h-4 w-px bg-gray-200"></span>
                <span class="text-sm font-semibold text-gray-900 tabular-nums" x-text="nowText">--</span>
            </div>
        </div>

        @php
        $queueStats = $queueStats ?? [
        'total' => 0,
        'menunggu' => 0,
        'selesai' => 0,
        'dibatalkan' => 0,
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Total Antrian --}}
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Total Antrian</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $queueStats['total'] ?? 0 }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg">
                        <span class="icon-[tabler--calendar-stats] w-5 h-5 text-primary-600"></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Jumlah seluruh pendaftaran antrian hari ini.</p>
            </div>

            {{-- Sisa Antrian --}}
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Sisa Antrian</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $queueStats['menunggu'] ?? 0 }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-lg">
                        <span class="icon-[tabler--clock-hour-3] w-5 h-5 text-yellow-600"></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Antrian yang belum diproses/selesai.</p>
            </div>

            {{-- Selesai --}}
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Sudah Selesai</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $queueStats['selesai'] ?? 0 }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                        <span class="icon-[tabler--circle-check] w-5 h-5 text-green-600"></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Antrian yang sudah diproses sampai selesai.</p>
            </div>

            {{-- Dibatalkan --}}
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Dibatalkan</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $queueStats['dibatalkan'] ?? 0 }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg">
                        <span class="icon-[tabler--circle-x] w-5 h-5 text-red-600"></span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Antrian yang dibatalkan pada hari ini.</p>
            </div>
        </div>
    </section>

    {{-- SECTION 2: Audit Log --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Audit Log</h3>
                <p class="text-sm text-gray-500">Riwayat perubahan pada entitas pendaftaran & pemeriksaan.</p>
            </div>
        </div>

        @php
        $auditLogs = $auditLogs ?? [];
        @endphp

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-center text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Entity</th>
                        <th scope="col" class="px-6 py-3">Changed By</th>
                        <th scope="col" class="px-6 py-3">Changed At</th>
                        <th scope="col" class="px-6 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($auditLogs as $i => $log)
                    @php
                    $entity = strtoupper($log['entity'] ?? $log->entity ?? '-');
                    $changedBy = $log['changed_by'] ?? $log->changed_by ?? '-';
                    $changedAt = $log['changed_at'] ?? $log->changed_at ?? '-';
                    $detail = $log['detail'] ?? $log->detail ?? '-';

                    $badgeClass = 'bg-gray-100 text-gray-800';
                    if (str_contains(strtolower($entity), 'pendaftaran')) $badgeClass = 'bg-blue-100 text-blue-800';
                    if (str_contains(strtolower($entity), 'pemeriksaan')) $badgeClass = 'bg-green-100 text-green-800';
                    @endphp

                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-900">
                            {{ is_int($i) ? $i + 1 : $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $badgeClass }}">
                                {{ $entity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $changedBy }}
                        </td>
                        <td class="px-6 py-4">
                            {{ formatDateTime($changedAt) }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ $detail }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center">
                            <p class="text-sm font-semibold text-gray-700">Belum ada riwayat perubahan</p>
                            <p class="text-xs text-gray-500 mt-1">Audit log akan tampil ketika ada perubahan data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Flowbite Pagination --}}
            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between p-4 bg-white border-t border-gray-200" aria-label="Table navigation">
                <span class="text-sm font-normal text-gray-500">
                    Halaman <span class="font-semibold text-gray-900">{{ $auditMeta['page'] }}</span>
                </span>
                <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
                    @if($auditMeta['hasPrev'])
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $auditMeta['page'] - 1]) }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                    </li>
                    @else
                    <li>
                        <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-white border border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                    </li>
                    @endif

                    @if($auditMeta['hasNext'])
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $auditMeta['page'] + 1]) }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                    </li>
                    @else
                    <li>
                        <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-white border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.petugas') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                Refresh
            </a>
        </div>
    </section>

</div>

{{-- Realtime Clock Script (Alpine) --}}
<script>
    function realtimeClock() {
        return {
            nowText: '--',
            timer: null,

            formatNow() {
                try {
                    const now = new Date();
                    const date = new Intl.DateTimeFormat('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: '2-digit',
                    }).format(now);

                    const time = new Intl.DateTimeFormat('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false,
                    }).format(now);

                    this.nowText = `${date} • ${time}`;
                } catch (e) {
                    const now = new Date();
                    this.nowText = now.toLocaleString();
                }
            },

            start() {
                this.formatNow();
                this.timer = setInterval(() => this.formatNow(), 1000);
            },
        }
    }
</script>
@endsection
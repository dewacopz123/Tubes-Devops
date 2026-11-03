@extends('layouts.app', ['sidebar' => 'dokter'])

@section('title', 'Hasil Pemeriksaan Pasien')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.dokter')],
        ['label' => 'Pasien', 'url' => route('pasien.dokter')],
        ['label' => 'Hasil Pemeriksaan', 'url' => null]
    ]" />

    {{-- HEADER --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Hasil Pemeriksaan</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if($pasienName)
                Pasien: <span class="font-semibold text-gray-900">{{ $pasienName }}</span>
                @else
                ID Pasien: <span class="font-semibold text-gray-900">{{ $pasienId }}</span>
                @endif
            </p>
        </div>

        <a href="{{ route('pasien.dokter') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100">
            <span class="icon-[tabler--arrow-left] w-3 h-3 me-2 rtl:rotate-180"></span>
            Kembali
        </a>
    </div>

    {{-- Flowbite Alert --}}
    @if($errorMessage)
    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
        <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
        <span class="sr-only">Error</span>
        <div>{{ $errorMessage }}</div>
    </div>
    @endif

    {{-- Flowbite Table --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">No</th>
                    <th scope="col" class="px-6 py-3 text-center">ID Pemeriksaan</th>
                    <th scope="col" class="px-6 py-3 text-center">ID Pendaftaran</th>
                    <th scope="col" class="px-6 py-3">Kategori</th>
                    <th scope="col" class="px-6 py-3 text-center">Tgl Pemeriksaan</th>
                    <th scope="col" class="px-6 py-3">Catatan</th>
                    <th scope="col" class="px-6 py-3 text-center">Hasil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                @php
                $examId = $exam['pemeriksaan_id'] ?? $exam['id'] ?? '-';
                $fileUrl = $exam['file_url'] ?? $exam['file'] ?? null;
                @endphp

                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-center text-gray-900">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-6 py-4 text-center font-medium text-gray-900">
                        {{ $examId }}
                    </td>

                    <td class="px-6 py-4 text-center text-gray-900">
                        {{ $exam['pendaftaran_id'] ?? '-' }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $exam['kategori_nama'] ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        {{ $exam['tgl_pemeriksaan'] ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="line-clamp-2">
                            {{ $exam['catatan'] ?? '-' }}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php
                        $downloadUrl = $exam['download_url'] ?? null;
                        @endphp

                        @if($downloadUrl)
                        <a href="{{ $downloadUrl }}" target="_blank" class="inline-flex items-center font-medium text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-xs px-3 py-2">
                            <span class="icon-[tabler--download] w-3 h-3 me-1.5"></span>
                            Download
                        </a>
                        @else
                        <span class="text-xs text-gray-400">Tidak ada file</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-gray-700">
                            Belum ada hasil pemeriksaan
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Hanya pemeriksaan dengan status HASIL_TERSEDIA yang ditampilkan.
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
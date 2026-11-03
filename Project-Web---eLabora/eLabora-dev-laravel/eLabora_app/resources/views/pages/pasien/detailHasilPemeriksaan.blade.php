@extends('layouts.app', ['sidebar' => 'pasien'])

@section('title', 'Detail Hasil Pemeriksaan Pasien')

@section('content')
    <div class="px-6 py-6 max-w-3xl">

        {{-- Breadcrumb Navigation --}}
        <x-breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('pasien.dashboard')],
            ['label' => 'Hasil Pemeriksaan', 'url' => route('hasil.pemeriksaan')],
            ['label' => 'Detail', 'url' => null]
        ]" />

        {{-- Flowbite Back Button --}}
        <a href="{{ url('/hasil-pemeriksaan') }}" class="inline-flex items-center px-4 py-2 mb-4 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100">
            <span class="icon-[tabler--arrow-left] w-3 h-3 me-2 rtl:rotate-180"></span>
            Kembali
        </a>

        {{-- Flowbite Card --}}
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">

            <h2 class="text-xl font-bold text-gray-900 mb-6">Detail Pemeriksaan</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-y-5">

                <p class="text-sm font-medium text-gray-500">Nama Pasien</p>
                <p class="md:col-span-2 text-gray-900">
                    {{ $data['pasien_nama'] ?? '-' }}
                </p>

                <p class="text-sm font-medium text-gray-500">Jenis Pemeriksaan</p>
                <p class="md:col-span-2 text-gray-900">
                    Pemeriksaan {{ $data['kategori_nama'] ?? '-' }}
                </p>

                <p class="text-sm font-medium text-gray-500">Kategori</p>
                <div class="md:col-span-2">
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        {{ $data['kategori_nama'] ?? '-' }}
                    </span>
                </div>

                <p class="text-sm font-medium text-gray-500">Status</p>
                <div class="md:col-span-2">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        {{ str_replace('_', ' ', $data['status_hasil'] ?? '-') }}
                    </span>
                </div>

                <p class="text-sm font-medium text-gray-500">Tanggal & Waktu</p>
                <p class="md:col-span-2 text-gray-900">
                    {{ formatDateTime($data['tgl_pemeriksaan'] ?? null) }}
                </p>

                <p class="text-sm font-medium text-gray-500">No. Laboratorium</p>
                <p class="md:col-span-2 text-gray-900">
                    {{ $data['no_lab'] ?? '-' }}
                </p>

            </div>
        </div>

        {{-- Surat Rujukan Download Section --}}
        <div class="mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Surat Rujukan</h3>

            @if(!empty($data['pendaftaran_id']))
                <a href="{{ route('registrations.download', ['id' => $data['pendaftaran_id']]) }}" 
                   target="_blank"
                   class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-100">
                    <div class="flex items-center">
                        <span class="icon-[tabler--download] w-5 h-5 text-gray-500 me-3"></span>
                        <div>
                            <span class="text-sm text-gray-700 block">Surat Rujukan Pemeriksaan</span>
                            <span class="text-xs text-gray-500">PDF Document</span>
                        </div>
                    </div>

                    <span class="inline-flex items-center text-sm font-medium text-primary-600 hover:underline">
                        Download
                        <span class="icon-[tabler--arrow-right] w-3 h-3 ms-2 rtl:rotate-180"></span>
                    </span>
                </a>
            @else
                <div class="flex items-center p-4 text-sm text-gray-800 border border-gray-300 rounded-lg bg-gray-50" role="alert">
                    <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
                    <span class="sr-only">Info</span>
                    <div>Surat rujukan tidak tersedia.</div>
                </div>
            @endif
        </div>

        {{-- File Downloads Section --}}
        <div class="mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">File Hasil Pemeriksaan</h3>

            @if(!empty($data['files']) && count($data['files']) > 0)
                <div class="space-y-2">
                    @foreach ($data['files'] as $file)
                        <a href="{{ route('pemeriksaan.file.download', ['examId' => $examId, 'fileId' => $file['id']]) }}" target="_blank" class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center">
                                <span class="icon-[tabler--download] w-5 h-5 text-gray-500 me-3"></span>
                                <div>
                                    <span class="text-sm text-gray-700 block">
                                        {{ $file['blob_name'] ?? 'File Hasil' }}
                                    </span>
                                    @if(!empty($file['file_type']))
                                        <span class="text-xs text-gray-500">
                                            {{ $file['file_type'] }} 
                                            @if(!empty($file['size_bytes']))
                                                • {{ number_format($file['size_bytes'] / 1024, 2) }} KB
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <span class="inline-flex items-center text-sm font-medium text-primary-600 hover:underline">
                                Download
                                <span class="icon-[tabler--arrow-right] w-3 h-3 ms-2 rtl:rotate-180"></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="flex items-center p-4 text-sm text-gray-800 border border-gray-300 rounded-lg bg-gray-50" role="alert">
                    <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
                    <span class="sr-only">Info</span>
                    <div>Belum ada file hasil pemeriksaan.</div>
                </div>
            @endif
        </div>

    </div>
@endsection
@extends('layouts.app', ['sidebar' => 'dokter'])

@section('title', 'Pasien')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.dokter')],
        ['label' => 'Pasien', 'url' => null]
    ]" />

    {{-- HEADER --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pasien</h2>
            <p class="mt-1 text-sm text-gray-500">Daftar pasien yang dapat diakses oleh dokter.</p>
        </div>

        {{-- Flowbite Search Form --}}
        <form method="GET" action="{{ route('pasien.dokter') }}" class="w-full sm:w-[520px]">
            <div class="flex gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <span class="icon-[tabler--search] w-4 h-4 text-gray-500"></span>
                    </div>
                    <input type="search" name="q" value="{{ $q ?? '' }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari nama / NIK / no. telepon..." />
                </div>

                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2">
                    Cari
                </button>

                @if(($q ?? '') !== '')
                    <a href="{{ route('pasien.dokter') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Flowbite Alert --}}
    @if(!empty($errorMessage))
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
                    <th scope="col" class="px-6 py-3">Pasien</th>
                    <th scope="col" class="px-6 py-3 text-center">NIK</th>
                    <th scope="col" class="px-6 py-3 text-center">Username</th>
                    <th scope="col" class="px-6 py-3 text-center">No. Telepon</th>
                    <th scope="col" class="px-6 py-3 text-center">Email</th>
                    <th scope="col" class="px-6 py-3 text-center">Tgl Lahir</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $p)
                    @php
                        $id = $p['id'] ?? null;
                    @endphp

                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-center text-gray-900">{{ $loop->iteration }}</td>

                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a href="{{ route('dokter.pasien.pemeriksaan', ['id' => $id]) }}" class="hover:underline text-primary-600">
                                {{ $p['nama'] ?? '-' }}
                            </a>
                            <div class="text-xs text-gray-500 font-normal">ID: {{ $id }}</div>
                        </th>

                        <td class="px-6 py-4 text-center">{{ $p['nik'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ $p['username'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ $p['no_telepon'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ $p['email'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            {{ isset($p['tgl_lahir']) ? substr($p['tgl_lahir'],0,10) : '-' }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('dokter.pasien.pemeriksaan', ['id' => $id]) }}" class="font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 rounded-lg text-xs px-3 py-2 text-center inline-flex items-center">
                                Lihat Pemeriksaan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center">
                            <p class="text-sm font-semibold text-gray-700">Belum ada data pasien</p>
                            <p class="text-xs text-gray-500 mt-1">Data pasien akan muncul di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

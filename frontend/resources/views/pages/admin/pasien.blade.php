@extends('layouts.app', ['sidebar' => 'admin'])

@section('title', 'Manajemen Pasien')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.petugas')],
        ['label' => 'Manajemen Pasien', 'url' => null]
    ]" />

    {{-- HEADER --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Pasien</h2>
            <p class="mt-1 text-sm text-gray-500">Daftar seluruh pasien yang terdaftar pada sistem.</p>
        </div>

        {{-- Flowbite Search Form --}}
        <form method="GET" action="{{ route('petugas.pasien') }}" class="w-full sm:w-[520px]">
            <div class="flex gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" name="q" value="{{ $q ?? '' }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari nama / NIK / no. telepon..." />
                </div>

                {{-- Preserve sort parameter --}}
                @if(!empty(request('sort')))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(!empty(request('order')))
                <input type="hidden" name="order" value="{{ request('order') }}">
                @endif

                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2">
                    Cari
                </button>

                @if(($q ?? '') !== '' || !empty(request('sort')))
                <a href="{{ route('petugas.pasien') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Flowbite Alerts --}}
    @if(!empty($errorMessage))
    <div class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
        <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
        <span class="sr-only">Error</span>
        <div>{{ $errorMessage }}</div>
    </div>
    @endif

    @if(session('success'))
    <div class="flex items-center p-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
        <span class="icon-[tabler--check] flex-shrink-0 inline w-4 h-4 me-3"></span>
        <span class="sr-only">Success</span>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
        <span class="icon-[tabler--alert-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
        <span class="sr-only">Error</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    {{-- Flowbite Table --}}
    <div class="relative shadow-md sm:rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center">No</th>
                        <th scope="col" class="px-6 py-3">
                            <a href="{{ route('petugas.pasien', array_merge(request()->all(), ['sort' => 'nama', 'order' => (request('sort') === 'nama' && request('order') === 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center hover:text-indigo-600">
                                Pasien
                                @if(request('sort') === 'nama')
                                    <span class="icon-[tabler--chevron-{{ request('order') === 'asc' ? 'up' : 'down' }}] w-4 h-4 ml-1"></span>
                                @else
                                    <span class="icon-[tabler--selector] w-4 h-4 ml-1 text-gray-400"></span>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            <a href="{{ route('petugas.pasien', array_merge(request()->all(), ['sort' => 'nik', 'order' => (request('sort') === 'nik' && request('order') === 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center justify-center hover:text-indigo-600">
                                NIK
                                @if(request('sort') === 'nik')
                                    <span class="icon-[tabler--chevron-{{ request('order') === 'asc' ? 'up' : 'down' }}] w-4 h-4 ml-1"></span>
                                @else
                                    <span class="icon-[tabler--selector] w-4 h-4 ml-1 text-gray-400"></span>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">Username</th>
                        <th scope="col" class="px-6 py-3 text-center">No. Telepon</th>
                        <th scope="col" class="px-6 py-3 text-center">Email</th>
                        <th scope="col" class="px-6 py-3 text-center">Tgl Lahir</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($patients ?? []) as $p)
                    @php
                    $id = $p['id'] ?? null;
                    $nama = $p['nama'] ?? '-';
                    $nik = $p['nik'] ?? '-';
                    $username = $p['username'] ?? '-';
                    $telp = $p['no_telepon'] ?? '-';
                    $email = $p['email'] ?? '-';
                    $tglLahir = $p['tgl_lahir'] ?? null;
                    $tglLahirText = formatDate($tglLahir);
                    @endphp

                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center text-gray-900 font-medium">
                            {{ $loop->iteration + (($meta['page'] ?? 1) - 1) * ($meta['limit'] ?? 20) }}
                        </td>

                        <th scope="row" class="px-6 py-4 font-semibold text-gray-900">
                            @if($id)
                            <a href="{{ route('petugas.pasien.pemeriksaan', ['id' => $id]) }}" class="hover:text-indigo-600 hover:underline" title="Lihat pemeriksaan pasien">
                                {{ $nama }}
                            </a>
                            @else
                            {{ $nama }}
                            @endif
                            @if($id)
                            <div class="text-xs text-gray-500 font-normal mt-0.5">ID: {{ $id }}</div>
                            @endif
                        </th>

                        <td class="px-6 py-4 text-center text-gray-600">{{ $nik }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $username }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $telp }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $email }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $tglLahirText }}</td>

                        <td class="px-6 py-4 text-center">
                            @if($id)
                            <a href="{{ route('petugas.pasien.pemeriksaan', ['id' => $id]) }}" class="inline-flex items-center font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 rounded-lg text-xs px-3 py-2" title="Lihat Pendaftaran & Pemeriksaan Pasien">
                                <span class="icon-[tabler--eye] w-3.5 h-3.5 me-1.5"></span>
                                Pendaftaran & Pemeriksaan
                            </a>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                    <span class="icon-[tabler--users] w-8 h-8 text-gray-400"></span>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">
                                    @if(($q ?? '') !== '')
                                        Tidak ada hasil pencarian
                                    @else
                                        Belum ada data pasien
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    @if(($q ?? '') !== '')
                                        Coba gunakan kata kunci lain atau reset pencarian
                                    @else
                                        Data akan muncul setelah pasien terdaftar di sistem
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination & Meta Info --}}
    @if(!empty($meta) && !empty($patients) && count($patients) > 0)
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        {{-- Meta Info --}}
        <div class="text-sm text-gray-600">
            Menampilkan <span class="font-semibold text-gray-900">{{ count($patients) }}</span> dari <span class="font-semibold text-gray-900">{{ $meta['total'] ?? 0 }}</span> pasien
        </div>

        {{-- Pagination Controls --}}
        @if(($meta['total'] ?? 0) > ($meta['limit'] ?? 20))
        <nav aria-label="Page navigation">
            <ul class="inline-flex -space-x-px text-sm">
                {{-- Previous Button --}}
                <li>
                    @if(($meta['page'] ?? 1) > 1)
                    <a href="{{ route('petugas.pasien', array_merge(request()->all(), ['page' => ($meta['page'] - 1)])) }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">
                        <span class="icon-[tabler--chevron-left] w-4 h-4"></span>
                    </a>
                    @else
                    <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-300 bg-gray-50 border border-gray-300 rounded-s-lg cursor-not-allowed">
                        <span class="icon-[tabler--chevron-left] w-4 h-4"></span>
                    </span>
                    @endif
                </li>

                {{-- Page Numbers --}}
                @php
                    $currentPage = $meta['page'] ?? 1;
                    $totalPages = ceil(($meta['total'] ?? 0) / ($meta['limit'] ?? 20));
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                @endphp

                @for($i = $startPage; $i <= $endPage; $i++)
                <li>
                    <a href="{{ route('petugas.pasien', array_merge(request()->all(), ['page' => $i])) }}" class="flex items-center justify-center px-3 h-8 leading-tight {{ $i === $currentPage ? 'text-indigo-600 border border-indigo-300 bg-indigo-50 hover:bg-indigo-100' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700' }}">
                        {{ $i }}
                    </a>
                </li>
                @endfor

                {{-- Next Button --}}
                <li>
                    @if($currentPage < $totalPages)
                    <a href="{{ route('petugas.pasien', array_merge(request()->all(), ['page' => ($currentPage + 1)])) }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">
                        <span class="icon-[tabler--chevron-right] w-4 h-4"></span>
                    </a>
                    @else
                    <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-300 bg-gray-50 border border-gray-300 rounded-e-lg cursor-not-allowed">
                        <span class="icon-[tabler--chevron-right] w-4 h-4"></span>
                    </span>
                    @endif
                </li>
            </ul>
        </nav>
        @endif
    </div>
    @endif

</div>
@endsection
@extends('layouts.app', ['sidebar' => 'admin'])

@section('title', 'Manajemen Pemeriksaan')

@section('content')

<div
    x-data="{
        openCreate: false,
        openDetail: false,
        openUpload: false,
        openDelete: false,
        selectedExam: null,
        filterStatusValidasi: 'ALL',
        filterStatusHasil: 'ALL',
        filterKategori: 'ALL',
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        toast(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 5000);
        }
    }"
    class="space-y-6">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard.petugas')],
        ['label' => 'Pemeriksaan', 'url' => null]
    ]" />

    {{-- HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Pemeriksaan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data pemeriksaan, pembaruan status, dan upload file.</p>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Filter:</span>
            
            {{-- Status Validasi Filter --}}
            <select x-model="filterStatusValidasi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 pl-3 pr-10 py-2">
                <option value="ALL">Semua Validasi</option>
                <option value="DRAFT">DRAFT</option>
                <option value="TERVALIDASI">TERVALIDASI</option>
            </select>

            {{-- Status Hasil Filter --}}
            <select x-model="filterStatusHasil" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 pl-3 pr-10 py-2">
                <option value="ALL">Semua Status Hasil</option>
                <option value="MENUNGGU_HASIL">MENUNGGU_HASIL</option>
                <option value="HASIL_TERSEDIA">HASIL_TERSEDIA</option>
                <option value="TIDAK_TERSEDIA">TIDAK_TERSEDIA</option>
            </select>

            {{-- Kategori Filter --}}
            <select x-model="filterKategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 pl-3 pr-10 py-2">
                <option value="ALL">Semua Kategori</option>
                @foreach($kategoriList ?? [] as $kat)
                    <option value="{{ $kat['nama'] }}">{{ $kat['nama'] }}</option>
                @endforeach
            </select>

            {{-- Reset Filters --}}
            <button @click="filterStatusValidasi = 'ALL'; filterStatusHasil = 'ALL'; filterKategori = 'ALL';" class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                <span class="icon-[tabler--refresh] inline w-4 h-4 mr-1"></span>
                Reset
            </button>
        </div>
    </div>

    {{-- Flowbite Alerts --}}
    @if(session('success'))
    <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Success</span>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Error</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    {{-- ACTION BAR --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Search --}}
        <form method="GET" action="{{ url()->current() }}" class="flex w-full sm:w-auto gap-2">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" name="q" value="{{ $q ?? '' }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari pemeriksaan (ID / NIK / nama pasien)">
            </div>

            @if(!is_null($statusHasil ?? null) && $statusHasil !== '')
            <input type="hidden" name="status_hasil" value="{{ $statusHasil }}">
            @endif

            <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2">
                Cari
            </button>

            @if(($q ?? '') !== '')
            <a href="{{ url()->current() }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                Reset
            </a>
            @endif
        </form>

        <button @click="openCreate = true" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            + Buat Pemeriksaan
        </button>
    </div>

    {{-- Flowbite Table --}}
    <div class="relative shadow-md sm:rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">ID Pendaftaran</th>
                    <th scope="col" class="px-6 py-3 text-left">Kategori</th>
                    <th scope="col" class="px-6 py-3">Tgl Pemeriksaan</th>
                    <th scope="col" class="px-6 py-3">Status Validasi</th>
                    <th scope="col" class="px-6 py-3">Status Hasil</th>
                    <th scope="col" class="px-6 py-3 text-left">Catatan</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($exams ?? [] as $i => $exam)
                @php
                $page = (int) data_get($meta ?? [], 'page', 1);
                $limit = (int) data_get($meta ?? [], 'limit', 20);
                $no = (($page - 1) * $limit) + $i + 1;
                $statusValidasi = strtoupper($exam['status_validasi'] ?? '');
                $statusHasil = strtoupper($exam['status_hasil'] ?? '');
                $kategoriNama = $exam['kategori_nama'] ?? '';
                @endphp

                <tr x-show="(filterStatusValidasi === 'ALL' || filterStatusValidasi === '{{ $statusValidasi }}') && (filterStatusHasil === 'ALL' || filterStatusHasil === '{{ $statusHasil }}') && (filterKategori === 'ALL' || filterKategori === '{{ $kategoriNama }}')" class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900">{{ $no }}</td>
                    <td class="px-6 py-4">{{ $exam['pendaftaran_id'] ?? '-' }}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-left">
                        {{ $kategoriNama }}
                    </th>
                    <td class="px-6 py-4">{{ formatDate($exam['tgl_pemeriksaan'] ?? null) }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $statusValidasi === 'TERVALIDASI' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $statusValidasi }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $statusHasil === 'HASIL_TERSEDIA' ? 'bg-green-100 text-green-800' : ($statusHasil === 'MENUNGGU_HASIL' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $statusHasil }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-left">
                        <div class="line-clamp-2">{{ $exam['catatan'] ?? '-' }}</div>
                    </td>

                    <td class="px-6 py-4">
                        {{-- Action Dropdown Menu --}}<div x-data="{ open: false, isBottom: false }" 
                             @click.away="open = false" 
                             x-init="$watch('open', value => { if(value) { const rect = $el.getBoundingClientRect(); isBottom = rect.bottom > window.innerHeight - 200; } })"
                             class="relative inline-block text-left">
                            <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100">
                                Aksi
                                <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition 
                                 :class="isBottom ? 'bottom-full mb-2' : 'top-full mt-2'"
                                 class="absolute right-0 z-50 w-48 rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                 style="display: none;">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li>
                                        <button @click="open = false; openDetail = true; selectedExam = {{ json_encode($exam) }}" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                            <span class="icon-[tabler--edit] w-4 h-4"></span>
                                            Detail / Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="open = false; openUpload = true; selectedExam = {{ json_encode($exam) }}" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                            <span class="icon-[tabler--upload] w-4 h-4"></span>
                                            Upload File
                                        </button>
                                    </li>
                                    <li>
                                        <button @click="open = false; openDelete = true; selectedExam = {{ json_encode($exam) }}" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600 flex items-center gap-2">
                                            <span class="icon-[tabler--trash] w-4 h-4"></span>
                                            Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                        Tidak ada data pemeriksaan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Flowbite Modal CREATE --}}
    <div x-show="openCreate" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Buat Pemeriksaan</h3>
                    <button @click="openCreate=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form method="POST" action="{{ url('/pemeriksaan') }}" class="p-4 md:p-5">
                    @csrf
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="pendaftaran_id" class="block mb-2 text-sm font-medium text-gray-900">Pendaftaran ID</label>
                            <input type="number" name="pendaftaran_id" id="pendaftaran_id" required value="{{ old('pendaftaran_id') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Masukkan ID pendaftaran">
                        </div>
                        <div>
                            <label for="kategori_id" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                            <select name="kategori_id" id="kategori_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                @foreach($kategoriList ?? [] as $kat)
                                <option value="{{ $kat['id'] }}" @selected(old('kategori_id')==$kat['id'])>{{ $kat['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="catatan" class="block mb-2 text-sm font-medium text-gray-900">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500" placeholder="(Opsional)">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Flowbite Modal DETAIL/EDIT --}}
    <div x-show="openDetail" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" aria-hidden="true" x-data="{ activeTab: 'form' }">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Detail / Edit Pemeriksaan</h3>
                    <button @click="openDetail=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Tabs --}}
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'form'" :class="activeTab === 'form' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 text-sm font-medium border-b-2">Formulir</button>
                    <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 text-sm font-medium border-b-2">Info</button>
                </div>

                <div class="p-4 md:p-5">
                    <div x-show="activeTab === 'form'">
                        <form method="POST" :action="`{{ url('/pemeriksaan') }}/${selectedExam.pemeriksaan_id}`">
                            @csrf
                            @method('PATCH')
                            <div class="grid gap-4 mb-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Status Validasi</label>
                                    <select name="status_validasi" x-model="selectedExam.status_validasi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                        <option value="DRAFT">DRAFT</option>
                                        <option value="TERVALIDASI">TERVALIDASI</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Status Hasil</label>
                                    <select name="status_hasil" x-model="selectedExam.status_hasil" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                        <option value="MENUNGGU_HASIL">MENUNGGU_HASIL</option>
                                        <option value="HASIL_TERSEDIA">HASIL_TERSEDIA</option>
                                        <option value="TIDAK_TERSEDIA">TIDAK_TERSEDIA</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Catatan</label>
                                    <textarea name="catatan" rows="3" x-model="selectedExam.catatan" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="w-full text-white bg-indigo-700 hover:bg-indigo-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Perubahan</button>
                        </form>
                    </div>
                    <div x-show="activeTab === 'info'" class="space-y-3">
                        <p class="text-sm"><span class="font-bold">ID Pendaftaran:</span> <span x-text="selectedExam?.pendaftaran_id"></span></p>
                        <p class="text-sm"><span class="font-bold">Kategori:</span> <span x-text="selectedExam?.kategori_nama"></span></p>
                        <p class="text-sm"><span class="font-bold">Tgl Pemeriksaan:</span> <span x-text="selectedExam?.tgl_pemeriksaan"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flowbite Modal UPLOAD --}}
    <div x-show="openUpload" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md max-h-full" x-data="{fileName: '', reset() { this.fileName = ''; const inp = this.$refs.fileInput; if (inp) inp.value = ''; }}" x-init="$watch('openUpload', (v) => { if (v) reset(); })">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Upload File Pemeriksaan</h3>
                    <button @click="openUpload=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data" :action="`{{ url('/pemeriksaan') }}/${selectedExam.pemeriksaan_id}/file`" class="p-4 md:p-5">
                    @csrf
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">File (PDF/JPG/PNG)</label>
                            <input x-ref="fileInput" type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="fileName = $event.target.files?.[0]?.name ?? ''">
                            <div class="flex items-center gap-3">
                                <button type="button" @click="$refs.fileInput.click()" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2">
                                    Choose File
                                </button>
                                <div class="text-sm text-gray-600 truncate flex-1">
                                    <template x-if="fileName">
                                        <span class="font-medium text-gray-900" x-text="fileName"></span>
                                    </template>
                                    <template x-if="!fileName">
                                        <span class="text-gray-400">Belum ada file dipilih</span>
                                    </template>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maks 5MB. Format: PDF, JPG, PNG.</p>
                        </div>
                    </div>
                    <button type="submit" :disabled="!fileName" :class="!fileName ? 'opacity-60 cursor-not-allowed' : ''" class="text-white inline-flex items-center bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Upload
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="openDelete" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <button @click="openDelete=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <span class="icon-[tabler--x] w-5 h-5"></span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <span class="icon-[tabler--alert-triangle] w-6 h-6 text-red-600"></span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 mb-2">
                                Apakah Anda yakin ingin menghapus pemeriksaan ini?
                            </p>
                            <p class="text-xs text-gray-500">
                                <strong>ID:</strong> <span x-text="selectedExam?.pemeriksaan_id"></span><br>
                                <strong>Kategori:</strong> <span x-text="selectedExam?.kategori_nama"></span>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-red-600 font-medium">
                        <span class="icon-[tabler--alert-circle] inline w-3 h-3 mr-1"></span>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
                <div class="flex items-center gap-2 p-4 md:p-5 border-t border-gray-200 rounded-b">
                    <button @click="openDelete = false" type="button" class="flex-1 text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Batal
                    </button>
                    <form :action="`{{ url('/pemeriksaan') }}/${selectedExam?.pemeriksaan_id}`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-5 right-5 z-50 max-w-xs p-4 rounded-lg shadow-lg border-2"
         :class="toastType === 'success' ? 'bg-white border-green-200' : 'bg-white border-red-200'"
         style="display: none;">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                 :class="toastType === 'success' ? 'bg-green-100' : 'bg-red-100'">
                <span x-show="toastType === 'success'" class="icon-[tabler--check] w-5 h-5 text-green-600"></span>
                <span x-show="toastType === 'error'" class="icon-[tabler--x] w-5 h-5 text-red-600" style="display: none;"></span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900" x-text="toastMessage"></p>
            </div>
            <button @click="showToast = false" class="text-gray-400 hover:text-gray-600">
                <span class="icon-[tabler--x] w-4 h-4"></span>
            </button>
        </div>
    </div>

</div>

@endsection
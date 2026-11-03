@extends('layouts.app', ['sidebar' => 'admin'])

@section('title', 'Pemeriksaan Pasien')

@section('content')
<div
    x-data="{
        openDetail: false,
        openUpload: false,
        selectedExam: null
    }"
    class="space-y-6">
    
    {{-- HEADER --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pemeriksaan Pasien</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if($pasienName)
                Daftar hasil pemeriksaan untuk pasien: <span class="font-semibold text-gray-900">{{ $pasienName }}</span>
                @else
                Daftar hasil pemeriksaan untuk pasien ID: <span class="font-semibold text-gray-900">{{ $pasienId }}</span>
                @endif
            </p>
        </div>

        <a href="{{ route('petugas.pasien') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100">
            <svg class="w-3 h-3 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
            Kembali
        </a>
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

    @if($errorMessage)
    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Error</span>
        <div>{{ $errorMessage }}</div>
    </div>
    @endif

    {{-- Registrations Section --}}
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Pendaftaran Pasien</h3>
                <p class="text-sm text-gray-500 mt-1">Pendaftaran yang dapat dibuatkan pemeriksaan</p>
            </div>
        </div>

        @if(!empty($registrations) && count($registrations) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($registrations as $reg)
            <div class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="icon-[tabler--file-text] w-5 h-5 text-indigo-600"></span>
                            <span class="text-sm font-semibold text-gray-900">ID: {{ $reg['id'] ?? '-' }}</span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-600">
                            <p><span class="font-medium">No. Antrian:</span> {{ $reg['no_antrian'] ?? '-' }}</p>
                            <p><span class="font-medium">No. Lab:</span> {{ $reg['no_lab'] ?? '-' }}</p>
                            <p><span class="font-medium">Tanggal:</span> {{ formatDate($reg['tanggal_antrian'] ?? null) }}</p>
                            <p><span class="font-medium">Status:</span> 
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ ($reg['status'] ?? '') === 'DISETUJUI' ? 'bg-green-100 text-green-800' : (($reg['status'] ?? '') === 'MENUNGGU' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $reg['status'] ?? '-' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('pemeriksaan.petugas') }}?pendaftaran_id={{ $reg['id'] }}" 
                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:ring-4 focus:outline-none focus:ring-indigo-300">
                        <span class="icon-[tabler--plus] w-3.5 h-3.5 me-1.5"></span>
                        Buat Pemeriksaan
                    </a>
                    @if(!empty($reg['file_path']))
                    <a href="{{ route('registrations.download', ['id' => $reg['id']]) }}" 
                       target="_blank"
                       class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-indigo-700 bg-white border border-indigo-300 hover:bg-indigo-50 rounded-lg focus:ring-4 focus:outline-none focus:ring-indigo-300"
                       title="Download Surat Rujukan">
                        <span class="icon-[tabler--download] w-3.5 h-3.5"></span>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-8">
            <div class="w-12 h-12 mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                <span class="icon-[tabler--file-off] w-6 h-6 text-gray-400"></span>
            </div>
            <p class="text-sm font-semibold text-gray-700">Belum ada pendaftaran</p>
            <p class="text-xs text-gray-500 mt-1">Pasien belum melakukan pendaftaran</p>
        </div>
        @endif
    </div>

    {{-- Examinations Section --}}
    <div class="space-y-3">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Riwayat Pemeriksaan</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar pemeriksaan yang sudah dibuat</p>
        </div>

    {{-- Flowbite Table --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-center text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">ID Pemeriksaan</th>
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
                @forelse($exams as $i => $exam)
                @php
                $examId = $exam['pemeriksaan_id'] ?? $exam['id'] ?? null;
                @endphp
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-gray-900">{{ $i+1 }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $examId ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $exam['pendaftaran_id'] ?? '-' }}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-left">
                        {{ $exam['kategori_nama'] ?? '-' }}
                    </th>
                    <td class="px-6 py-4">{{ $exam['tgl_pemeriksaan'] ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $exam['status_validasi'] ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $exam['status_hasil'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-left">
                        <div class="line-clamp-2">{{ $exam['catatan'] ?? '-' }}</div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            <button type="button" @click="openDetail = true; selectedExam = {{ json_encode($exam) }}" class="font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 rounded-lg text-xs px-3 py-2">
                                Detail / Edit
                            </button>

                            <button type="button" @click="openUpload = true; selectedExam = {{ json_encode($exam) }}" class="font-medium text-indigo-700 bg-white border border-indigo-300 hover:bg-indigo-50 focus:ring-4 focus:outline-none focus:ring-indigo-300 rounded-lg text-xs px-3 py-2">
                                Upload
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-gray-700">Belum ada pemeriksaan</p>
                        <p class="text-xs text-gray-500 mt-1">Data pemeriksaan pasien akan muncul setelah dibuat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    {{-- Flowbite Modal EDIT --}}
    <div x-show="openDetail" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50" aria-hidden="true">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Detail / Edit Pemeriksaan</h3>
                    <button @click="openDetail=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form method="POST" :action="`{{ route('petugas.pasien.exams.update', ['examId' => 0]) }}`.replace('/0', `/${selectedExam.pemeriksaan_id ?? selectedExam.id}`)" class="p-4 md:p-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="status_validasi_edit" class="block mb-2 text-sm font-medium text-gray-900">Status Validasi</label>
                            <select name="status_validasi" id="status_validasi_edit" x-model="selectedExam.status_validasi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                <option value="DRAFT">DRAFT</option>
                                <option value="TERVALIDASI">TERVALIDASI</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_hasil_edit" class="block mb-2 text-sm font-medium text-gray-900">Status Hasil</label>
                            <select name="status_hasil" id="status_hasil_edit" x-model="selectedExam.status_hasil" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                <option value="MENUNGGU_HASIL">MENUNGGU_HASIL</option>
                                <option value="HASIL_TERSEDIA">HASIL_TERSEDIA</option>
                                <option value="TIDAK_TERSEDIA">TIDAK_TERSEDIA</option>
                            </select>
                        </div>
                        <div>
                            <label for="catatan_edit2" class="block mb-2 text-sm font-medium text-gray-900">Catatan</label>
                            <textarea name="catatan" id="catatan_edit2" rows="3" x-model="selectedExam.catatan" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan
                    </button>
                </form>
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
                <form method="POST" enctype="multipart/form-data" :action="`{{ route('petugas.pasien.exams.upload', ['examId' => 0]) }}`.replace('/0', `/${selectedExam.pemeriksaan_id ?? selectedExam.id}`)" class="p-4 md:p-5">
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

</div>
@endsection
@extends('layouts.app', ['sidebar' => 'pasien'])

@section('title', 'Pendaftaran Pasien')

@section('content')
    <div class="max-w-5xl mx-auto">

        {{-- Breadcrumb Navigation --}}
        <x-breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('pasien.dashboard')],
            ['label' => 'Pendaftaran', 'url' => null]
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

            {{-- Flowbite Button Group --}}
            <div class="inline-flex rounded-lg shadow-sm" role="group">
                <a href="{{ url('/pendaftaran') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-s-lg hover:bg-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700">
                    Pendaftaran
                </a>
                <a href="{{ url('/antrian-pasien') }}" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700">
                    Antrian
                </a>
            </div>
        </div>

        {{-- Flowbite Alerts --}}
        @if (session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
                <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
                <span class="sr-only">Info</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3"></span>
                <span class="sr-only">Error</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="flex p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                <span class="icon-[tabler--info-circle] flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]"></span>
                <span class="sr-only">Errors</span>
                <div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">

            <h2 class="text-xl font-bold text-gray-900 mb-1">
                Form Pendaftaran Pemeriksaan Lab
            </h2>
            <p class="text-sm text-gray-500 mb-6">
                Isi formulir di bawah ini untuk mendaftar pemeriksaan laboratorium
            </p>

            <form class="space-y-6" method="POST" action="{{ route('pasien.pendaftaran.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Data Pasien --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pasien</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap</label>
                            <input type="text" id="nama" value="{{ data_get($profile, 'profil.nama') }}" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- NIK --}}
                        <div>
                            <label for="nik" class="block mb-2 text-sm font-medium text-gray-900">NIK / No Identitas</label>
                            <input type="text" id="nik" value="{{ data_get($profile, 'profil.nik') }}" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label for="tgl_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                            <input type="date" id="tgl_lahir" value="{{ data_get($profile, 'profil.tgl_lahir') }}" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label for="jenis_kelamin" class="block mb-2 text-sm font-medium text-gray-900">Jenis Kelamin</label>
                            <select id="jenis_kelamin" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ data_get($profile, 'profil.jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ data_get($profile, 'profil.jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- No Telepon --}}
                        <div>
                            <label for="no_telepon" class="block mb-2 text-sm font-medium text-gray-900">No. Telepon</label>
                            <input type="text" id="no_telepon" value="{{ data_get($profile, 'profil.no_telepon') }}" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" id="email" value="{{ data_get($profile, 'akun.email') }}" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- Surat Rujukan --}}
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="surat_rujukan">Surat Rujukan (PDF / JPG / PNG) <span class="text-red-600 font-semibold">*</span></label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="surat_rujukan" name="surat_rujukan" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <p class="mt-1 text-xs text-gray-500">Wajib diupload. Maksimal 5MB. Format: PDF, JPG, PNG</p>
                            
                            {{-- File Preview --}}
                            <div id="file-preview" class="hidden mt-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    {{-- Preview Image/Icon --}}
                                    <div id="preview-container" class="flex-shrink-0">
                                        <img id="preview-image" class="hidden w-20 h-20 object-cover rounded-lg border border-gray-300" alt="Preview">
                                        <div id="preview-pdf" class="hidden w-20 h-20 flex items-center justify-center bg-red-100 rounded-lg border border-red-300">
                                            <span class="icon-[tabler--file-type-pdf] w-10 h-10 text-red-600"></span>
                                        </div>
                                    </div>
                                    
                                    {{-- File Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p id="file-name" class="text-sm font-medium text-gray-900 truncate"></p>
                                        <p id="file-size" class="text-xs text-gray-500 mt-1"></p>
                                        <p id="file-error" class="hidden text-xs text-red-600 mt-1"></p>
                                    </div>
                                    
                                    {{-- Remove Button --}}
                                    <button type="button" id="remove-file" class="flex-shrink-0 text-gray-400 hover:text-red-600 focus:outline-none">
                                        <span class="icon-[tabler--x] w-5 h-5"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900">Alamat</label>
                            <textarea id="alamat" rows="3" readonly class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">{{ data_get($profile, 'profil.alamat') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Jadwal Pemeriksaan --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Pemeriksaan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tanggal --}}
                        <div>
                            <label for="tanggal_antrian" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pemeriksaan</label>
                            <input type="date" id="tanggal_antrian" name="tanggal_antrian" value="{{ old('tanggal_antrian', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        </div>

                        {{-- Jam --}}
                        <div>
                            <label for="jam_pemeriksaan" class="block mb-2 text-sm font-medium text-gray-900">Jam Pemeriksaan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                    <span class="icon-[tabler--clock] w-4 h-4 text-gray-500"></span>
                                </div>
                                <input type="time" id="jam_pemeriksaan" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" min="08:00" max="17:00" value="09:00" required />
                            </div>
                        </div>

                        {{-- Hidden field --}}
                        <input type="hidden" name="jadwal_pemeriksaan_at" id="jadwal_pemeriksaan_at" value="{{ old('jadwal_pemeriksaan_at') }}">

                        <p class="text-xs text-gray-500 md:col-span-2">
                            <span class="icon-[tabler--info-circle] inline w-3 h-3 mr-1"></span>
                            Jam operasional: 08:00 - 17:00 WIB. Pilih jam sesuai ketersediaan Anda.
                        </p>
                    </div>
                </div>

                {{-- Submit Button with Loading State --}}
                <div x-data="{ loading: false, showModal: false }">
                    <button 
                        type="button"
                        @click="showModal = true"
                        :disabled="loading"
                        :class="loading ? 'opacity-75 cursor-not-allowed' : ''"
                        class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!loading">Daftar Sekarang</span>
                        <span x-show="loading">Memproses...</span>
                    </button>

                    {{-- Confirmation Modal --}}
                    <div x-show="showModal" 
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
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Konfirmasi Pendaftaran
                                    </h3>
                                    <button type="button" @click="showModal = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                                        <span class="icon-[tabler--x] w-5 h-5"></span>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                
                                {{-- Modal Body --}}
                                <div class="p-4 md:p-5">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="icon-[tabler--info-circle] w-6 h-6 text-indigo-600"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Pastikan semua data yang Anda masukkan sudah benar. Setelah mendaftar, Anda akan mendapatkan nomor antrian.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 mb-4">
                                        <span class="icon-[tabler--alert-circle] inline w-3 h-3 mr-1"></span>
                                        Pastikan surat rujukan sudah diupload dengan benar.
                                    </p>
                                </div>
                                
                                {{-- Modal Footer --}}
                                <div class="flex items-center gap-2 p-4 md:p-5 border-t border-gray-200 rounded-b">
                                    <button 
                                        type="button" 
                                        @click="showModal = false" 
                                        class="flex-1 text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        Batal
                                    </button>
                                    <button 
                                        type="button"
                                        @click="loading = true; showModal = false; $el.closest('form').submit();"
                                        class="flex-1 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        Ya, Daftar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <script>
        (function () {
            // Jadwal pemeriksaan handler
            const tanggal = document.getElementById('tanggal_antrian');
            const jam = document.getElementById('jam_pemeriksaan');
            const hidden = document.getElementById('jadwal_pemeriksaan_at');

            function updateJadwal() {
                if (!tanggal.value || !jam.value) {
                    hidden.value = '';
                    return;
                }
                hidden.value = `${tanggal.value}T${jam.value}`;
            }

            tanggal.addEventListener('change', updateJadwal);
            jam.addEventListener('change', updateJadwal);
            updateJadwal();

            // File preview handler
            const fileInput = document.getElementById('surat_rujukan');
            const filePreview = document.getElementById('file-preview');
            const previewImage = document.getElementById('preview-image');
            const previewPdf = document.getElementById('preview-pdf');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const fileError = document.getElementById('file-error');
            const removeBtn = document.getElementById('remove-file');

            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            function showPreview(file) {
                // Validate file size
                if (file.size > MAX_FILE_SIZE) {
                    fileError.textContent = 'File terlalu besar! Maksimal 5MB.';
                    fileError.classList.remove('hidden');
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    previewImage.classList.add('hidden');
                    previewPdf.classList.add('hidden');
                    filePreview.classList.remove('hidden');
                    fileInput.value = ''; // Clear invalid file
                    return;
                }

                fileError.classList.add('hidden');
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);

                // Show preview based on file type
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove('hidden');
                        previewPdf.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    previewImage.classList.add('hidden');
                    previewPdf.classList.remove('hidden');
                }

                filePreview.classList.remove('hidden');
            }

            function clearPreview() {
                fileInput.value = '';
                filePreview.classList.add('hidden');
                previewImage.src = '';
                previewImage.classList.add('hidden');
                previewPdf.classList.add('hidden');
                fileName.textContent = '';
                fileSize.textContent = '';
                fileError.classList.add('hidden');
            }

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    showPreview(file);
                }
            });

            removeBtn.addEventListener('click', clearPreview);
        })();
    </script>

@endsection

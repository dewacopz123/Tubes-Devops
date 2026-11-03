<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - eLabora</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-slate-800">

    {{-- BACKGROUND IMAGE --}}
    <img
        src="{{ asset('assets/images/pexels-indraprojectsofficial-28570736.jpg') }}"
        alt=""
        class="fixed inset-0 -z-30 h-full w-full object-cover" />

    {{-- LIGHT MODERN OVERLAY --}}
    <div class="fixed inset-0 -z-20 bg-white/55"></div>
    <div class="fixed inset-0 -z-20 bg-gradient-to-b from-indigo-50/70 via-white/30 to-white/60"></div>

    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="relative w-full max-w-2xl">

            {{-- SOFT BLOBS --}}
            <div class="pointer-events-none absolute -inset-10 -z-10">
                <div class="absolute -top-10 -left-8 h-44 w-44 rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute -bottom-12 -right-10 h-52 w-52 rounded-full bg-sky-400/15 blur-3xl"></div>
            </div>

            {{-- GLASS CARD --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/45 backdrop-blur-3xl shadow-2xl ring-1 ring-white/70 border border-white/35 p-6">
                <div class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-black/5"></div>

                {{-- GLARE / SHINE --}}
                <div class="pointer-events-none absolute -top-28 -left-28 h-72 w-72 rotate-12 bg-white/45 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-28 -right-28 h-80 w-80 -rotate-12 bg-indigo-200/25 blur-3xl"></div>

                {{-- top gloss strip --}}
                <div class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-white/50 to-transparent"></div>

                {{-- subtle grain --}}
                <div class="pointer-events-none absolute inset-0 opacity-[0.07] mix-blend-overlay"
                    style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.7) 1px, transparent 0); background-size: 18px 18px;">
                </div>

                <div class="mb-6 text-center relative">
                    <img
                        src="{{ asset('assets/images/logo/Logo.png') }}"
                        alt="eLabora"
                        class="h-10 mx-auto mb-3 drop-shadow-sm" />

                    <h1 class="text-2xl font-semibold text-slate-900">Register Akun eLabora</h1>
                    <p class="mt-1 text-sm text-slate-700">Silahkan lengkapi data dibawah ini untuk membuat akun</p>
                </div>

                @if($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200/80 bg-rose-50/85 px-4 py-3 text-sm text-rose-700 relative">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4 relative">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-slate-800">Username</label>
                            <input
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                placeholder="username"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-800">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="contoh@mail.com"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-slate-800">Password</label>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="minimal 8 karakter"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-800">NIK</label>
                            <input
                                type="text"
                                name="nik"
                                value="{{ old('nik') }}"
                                required
                                maxlength="16"
                                placeholder="1234567890123456"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-slate-800">Nama Lengkap</label>
                            <input
                                type="text"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                placeholder="Nama sesuai KTP"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-800">Jenis Kelamin</label>
                            <select
                                name="jenis_kelamin"
                                required
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih</option>
                                <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-slate-800">Tanggal Lahir</label>
                            <input
                                type="date"
                                name="tgl_lahir"
                                value="{{ old('tgl_lahir') }}"
                                required
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-800">No. Telepon</label>
                            <input
                                type="text"
                                name="no_telepon"
                                value="{{ old('no_telepon') }}"
                                required
                                placeholder="08xxxxxxxxxx"
                                class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                       shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-800">Alamat</label>
                        <textarea
                            name="alamat"
                            rows="3"
                            required
                            placeholder="Alamat lengkap"
                            class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                     shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">{{ old('alamat') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition
                   shadow-lg shadow-indigo-600/25">
                        Register
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-700 relative">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-700 hover:underline">Login</a>
                </p>
            </div>

            <p class="mt-6 text-center text-xs text-slate-700">
                © {{ date('Y') }} eLabora
            </p>
        </div>
    </main>

</body>

</html>
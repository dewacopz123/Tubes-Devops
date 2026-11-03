<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - eLabora</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800">

  {{-- BACKGROUND IMAGE --}}
  <img
    src="{{ asset('assets/images/pexels-indraprojectsofficial-28570736.jpg') }}"
    alt=""
    class="fixed inset-0 -z-30 h-full w-full object-cover"
  />

  {{-- LIGHT MODERN OVERLAY --}}
  <div class="fixed inset-0 -z-20 bg-white/55"></div>
  <div class="fixed inset-0 -z-20 bg-gradient-to-b from-indigo-50/70 via-white/30 to-white/60"></div>

  <main class="min-h-screen flex items-center justify-center px-4">
    <div class="relative w-full max-w-md">

      {{-- SOFT BLOBS --}}
      <div class="pointer-events-none absolute -inset-10 -z-10">
        <div class="absolute -top-10 -left-8 h-40 w-40 rounded-full bg-indigo-400/20 blur-3xl"></div>
        <div class="absolute -bottom-10 -right-8 h-44 w-44 rounded-full bg-sky-400/15 blur-3xl"></div>
      </div>

      {{-- GLASS CARD --}}
      <div class="relative overflow-hidden rounded-2xl bg-white/45 backdrop-blur-3xl shadow-2xl ring-1 ring-white/70 border border-white/35 p-6">
        {{-- inner edge highlight --}}
        <div class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-black/5"></div>

        <div class="pointer-events-none absolute -top-24 -left-24 h-64 w-64 rotate-12 bg-white/45 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 -rotate-12 bg-indigo-200/25 blur-3xl"></div>

        {{-- subtle top gloss strip --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-white/50 to-transparent"></div>

        {{-- subtle grain --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.07] mix-blend-overlay"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.7) 1px, transparent 0); background-size: 18px 18px;">
        </div>

        <div class="mb-6 text-center relative">
          <img
            src="{{ asset('assets/images/logo/Logo.png') }}"
            alt="eLabora"
            class="h-10 mx-auto mb-3 drop-shadow-sm"
          />

          <h1 class="text-2xl font-semibold text-slate-900">Masuk ke eLabora</h1>
          <p class="mt-1 text-sm text-slate-700">Silakan login untuk melanjutkan</p>
        </div>

        {{-- ALERT --}}
        @if(session('error'))
          <div class="mb-4 rounded-xl border border-rose-200/80 bg-rose-50/85 px-4 py-3 text-sm text-rose-700 relative">
            {{ session('error') }}
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 rounded-xl border borders border-rose-200/80 bg-rose-50/85 px-4 py-3 text-sm text-rose-700 relative">
            <ul class="list-disc pl-5 space-y-1">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4 relative">
          @csrf

          <div>
            <label class="text-sm font-medium text-slate-800">Username</label>
            <input
              type="text"
              name="username"
              value="{{ old('username') }}"
              required
              autofocus
              placeholder="username"
              class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                     shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
            />
          </div>

          <div>
            <label class="text-sm font-medium text-slate-800">Password</label>
            <input
              type="password"
              name="password"
              required
              placeholder="••••••••"
              class="mt-1 w-full rounded-xl border border-white/80 bg-white/75 px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400
                     shadow-sm shadow-black/5 focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
            />
          </div>

          <button
            type="submit"
            class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition
                   shadow-lg shadow-indigo-600/25"
          >
            Login
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-700 relative">
          Belum punya akun?
          <a href="{{ route('register') }}" class="font-semibold text-indigo-700 hover:underline">
            Register
          </a>
        </p>
      </div>

      <p class="mt-6 text-center text-xs text-slate-700">
        © {{ date('Y') }} eLabora
      </p>
    </div>
  </main>

</body>
</html>

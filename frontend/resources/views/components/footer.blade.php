<footer class="border-t border-indigo-100 bg-white">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">

            {{-- Left: Brand --}}
            <div class="flex items-center gap-2 text-sm text-slate-600">
                <span class="font-semibold text-indigo-600">eLabora</span>
                <span>— Sistem Informasi Laboratorium</span>
            </div>

            {{-- Center: Copyright --}}
            <div class="text-sm text-slate-500">
                © {{ date('Y') }} <span class="font-medium text-slate-700">eLabora</span>. All rights reserved.
            </div>

            {{-- Right: Meta / Version --}}
            <div class="text-sm text-slate-500">
                <span class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-600 font-medium">
                    v1.0
                </span>
            </div>

        </div>
    </div>
</footer>

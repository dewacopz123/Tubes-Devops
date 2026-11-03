<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 lg:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white flex flex-col">
        <ul class="space-y-2 font-medium flex-1">
            @php
            $items = [
                ['label' => 'Beranda', 'url' => url('/dashboard-dokter'), 'icon' => 'icon-[tabler--home]'],
                ['label' => 'Pasien', 'url' => url('/pasien-dokter'), 'icon' => 'icon-[tabler--users]'],
            ];
            $currentUrl = url()->current();
            @endphp

            @foreach ($items as $item)
            @php
            $isActive = $currentUrl === $item['url'];
            @endphp
            <li>
                <a href="{{ $item['url'] }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-indigo-50 group {{ $isActive ? 'bg-indigo-100' : '' }}">
                    <span class="{{ $item['icon'] }} w-5 h-5 {{ $isActive ? 'text-indigo-600' : 'text-gray-500 group-hover:text-indigo-600' }}"></span>
                    <span class="ms-3 {{ $isActive ? 'text-indigo-600 font-semibold' : '' }}">{{ $item['label'] }}</span>
                </a>
            </li>
            @endforeach
        </ul>

        {{-- User Profile Card with Dropdown --}}
        <div class="pt-4 mt-4 border-t border-gray-200">
            <button type="button" class="flex items-center w-full p-3 text-left rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" data-dropdown-toggle="sidebar-user-dropdown" data-dropdown-placement="top">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-semibold">
                    {{ strtoupper(substr(session('auth_username') ?? 'G', 0, 1)) }}
                </div>
                <div class="flex-1 ms-3 text-left">
                    <p class="text-sm font-medium text-gray-900">
                        {{ session('auth_username') ?? 'Guest' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ session('auth_role') ?? '' }}
                    </p>
                </div>
                <span class="icon-[tabler--chevron-down] w-4 h-4 text-gray-500"></span>
            </button>

            {{-- Dropdown Menu --}}
            <div id="sidebar-user-dropdown" class="z-50 hidden my-2 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow">
                <ul class="py-2" role="none">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <span class="icon-[tabler--logout] w-4 h-4 me-2 text-gray-500"></span>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
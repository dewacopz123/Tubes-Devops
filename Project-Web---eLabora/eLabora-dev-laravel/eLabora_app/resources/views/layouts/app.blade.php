<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'eLabora')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50">

    {{-- NAVBAR --}}
    @include('components.navbar')

    {{-- SIDEBAR (if role specified) --}}
    @isset($sidebar)
        @include('components.sidebar.' . $sidebar)
    @endisset

    {{-- MAIN CONTENT --}}
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            @yield('content')
        </div>
    </div>

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Flowbite JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    
</body>
</html>

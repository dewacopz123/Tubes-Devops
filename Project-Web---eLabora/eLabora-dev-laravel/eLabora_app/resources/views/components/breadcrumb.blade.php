{{--
    Breadcrumb Component
    
    Usage:
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => '/dashboard'],
        ['label' => 'Pendaftaran', 'url' => null] // null = current page
    ]" />
--}}

@props(['items' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
        {{-- Home Icon --}}
        <li class="inline-flex items-center">
            @if(count($items) > 0)
                <a href="{{ $items[0]['url'] ?? '#' }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <span class="icon-[tabler--home] w-4 h-4 me-2"></span>
                    {{ $items[0]['label'] ?? 'Home' }}
                </a>
            @endif
        </li>

        {{-- Breadcrumb Items --}}
        @foreach(array_slice($items, 1) as $index => $item)
            <li>
                <div class="flex items-center">
                    <span class="icon-[tabler--chevron-right] w-4 h-4 text-gray-400 mx-1"></span>
                    
                    @if($item['url'])
                        {{-- Clickable item --}}
                        <a href="{{ $item['url'] }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ms-2">
                            {{ $item['label'] }}
                        </a>
                    @else
                        {{-- Current page (non-clickable) --}}
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>

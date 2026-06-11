@php
    $navigationMenu = navigations();
@endphp

<flux:sidebar class="bg-white border-r border-slate-200 h-screen w-64 overflow-y-auto">
    <button @click="sidebarOpen = false" class="lg:hidden absolute top-4 right-4 p-1 text-slate-400 hover:text-slate-600">
        <flux:icon name="x-mark" variant="mini" class="w-5 h-5" />
    </button>

    <div class="flex items-center gap-2 px-2 py-4">
        <div class="p-2 bg-indigo-600 rounded-xl text-white">
            <flux:icon name="command-line" variant="mini" class="w-5 h-5" />
        </div>
        <div>
            <span class="font-extrabold text-sm tracking-tight block text-slate-900">SAHARA TRAVEL</span>
            <span class="text-[11px] text-slate-400 font-bold block -mt-0.5">ERP Umroh v1.0</span>
        </div>
    </div>

    <div class="space-y-2 px-2">
        @foreach ($navigationMenu as $group)
            @php
                $filteredItems = array_filter($group['items'], function ($item) {
                    return is_null($item['role']) || (Auth::check() && Auth::user()->hasRole($item['role']));
                });

                $isInitiallyOpen = shouldGroupBeOpen($group);
                $itemCount = count($filteredItems);
            @endphp

            @if ($itemCount > 0)
                {{-- KONDISI 1: JIKA ANGGOTA GRUP LEBIH DARI 1 (RENDER DROPDOWN BER-IKON INDUK) --}}
                @if ($itemCount > 1)
                    <div x-data="{ open: {{ $isInitiallyOpen ? 'true' : 'false' }} }" class="space-y-1">

                        <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <flux:icon name="{{ $group['icon'] ?? 'folder' }}" variant="mini"
                                    class="w-4 h-4 text-slate-400 group-hover:text-slate-600" />
                                <span class="tracking-tight">{{ $group['heading'] }}</span>
                            </div>
                            <flux:icon name="chevron-down" variant="micro"
                                class="w-3 h-3 text-slate-400 transition-transform duration-200"
                                ::class="open ? 'rotate-0' : '-rotate-90'" />
                        </button>

                        <div x-show="open" x-transition class="space-y-0.5 pl-4 border-l border-slate-100 ml-5 mt-0.5">
                            @foreach ($filteredItems as $item)
                                @php $isActive = isRouteActive($item['route']); @endphp
                                <a href="{{ route($item['route']) }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-1.5 text-xs rounded-lg font-medium transition-all duration-150 group
                                    {{ $isActive
                                        ? 'text-indigo-600 font-bold bg-indigo-50/70'
                                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">

                                    <span
                                        class="w-1.5 h-1.5 rounded-full transition-all duration-150 shrink-0
                                        {{ $isActive ? 'bg-indigo-600 scale-125 ring-4 ring-indigo-100' : 'bg-slate-300 group-hover:bg-slate-400' }}">
                                    </span>

                                    <span>{{ $item['title'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- KONDISI 2: JIKA ANGGOTA HANYA 1 (LANGSUNG RENDER LINK INDUK TANPA ANAK DROPDOWN) --}}
                @else
                    @foreach ($filteredItems as $item)
                        @php $isActive = isRouteActive($item['route']); @endphp
                        <a href="{{ route($item['route']) }}" wire:navigate
                            class="flex items-center justify-between px-3 py-2 text-sm rounded-xl font-semibold transition-all duration-150 group
                            {{ $isActive
                                ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20 scale-[1.02]'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                            <div class="flex items-center gap-2.5">
                                <flux:icon name="{{ $group['icon'] ?? 'folder' }}" variant="mini"
                                    class="w-4 h-4 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' }}" />
                                <span>{{ $item['title'] }}</span>
                            </div>
                        </a>
                    @endforeach
                @endif
            @endif
        @endforeach
    </div>

    <flux:spacer />

    @auth
        <div class="p-2 mx-2 mb-2 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3">
            <div
                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 text-xs shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-semibold text-slate-800 block truncate">{{ Auth::user()->name }}</span>
                <span class="text-[10px] text-slate-400 uppercase font-bold block truncate tracking-wider">
                    {{ str_replace('_', ' ', Auth::user()->roles->first()?->name ?? 'Staff') }}
                </span>
            </div>
        </div>
    @endauth
</flux:sidebar>

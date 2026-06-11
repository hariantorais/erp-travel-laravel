@props(['name', 'title' => 'Form Input', 'maxWidth' => 'max-w-lg'])

<div x-data="{
    isOpen: @entangle($name),
}"
    x-on:open-modal.window="
        if ($event.detail.name === '{{ $name }}') { 
            isOpen = true; 
        }
    "
    x-on:close-modal.window="
        if ($event.detail.name === '{{ $name }}') { 
            isOpen = false; 
        }
    "
    x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;"
    x-on:keydown.escape.window="isOpen = false">
    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

    <div x-show="isOpen" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" {{-- KUNCI 1: flex flex-col dan max-h mengunci tinggi modal maksimal 85% dari tinggi viewport --}}
        class="w-full {{ $maxWidth }} max-h-[85vh] bg-white rounded-2xl shadow-2xl border border-slate-200 relative z-10 flex flex-col overflow-hidden"
        x-on:click.away="isOpen = false">
        <div class="flex justify-between items-start p-6 pb-4 border-b border-slate-100 shrink-0">
            <div>
                <flux:heading size="lg" class="font-bold">{{ $title }}</flux:heading>
                @if (isset($subtitle))
                    <flux:subheading class="text-xs mt-0.5">{{ $subtitle }}</flux:subheading>
                @endif
            </div>
            <button type="button" x-on:click="isOpen = false"
                class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                <flux:icon name="x-mark" variant="micro" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
            {{ $slot }}
        </div>

    </div>
</div>

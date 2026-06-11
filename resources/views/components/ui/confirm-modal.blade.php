@props([
    'name',
    'title' => 'Konfirmasi Aksi',
    'variant' => 'danger' {{-- danger, warning, info --}}
])

<div 
    x-data="{ 
        isOpen: false,
        payload: null,
        confirmAction: null
    }" 
    x-on:open-confirm.window="
        if ($event.detail.name === '{{ $name }}') { 
            isOpen = true; 
            payload = $event.detail.payload || null;
            confirmAction = $event.detail.action || null;
        }
    "
    x-on:close-confirm.window="
        if ($event.detail.name === '{{ $name }}') { 
            isOpen = false; 
            payload = null;
            confirmAction = null;
        }
    "
    x-show="isOpen" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
    style="display: none;"
    x-on:keydown.escape.window="isOpen = false"
>
    <div 
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-2xl border border-slate-100 shadow-2xl p-6 w-full max-w-md space-y-4"
        @click.away="isOpen = false"
    >
        <div class="flex items-start gap-4">
            <div class="p-3 rounded-xl shrink-0 {{ $variant === 'danger' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }}">
                <flux:icon name="{{ $variant === 'danger' ? 'trash' : 'exclamation-triangle' }}" variant="mini" class="w-6 h-6" />
            </div>

            <div class="space-y-1">
                <h3 class="text-sm font-bold text-slate-900">{{ $title }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ $slot }}
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-50">
            <flux:button @click="isOpen = false" variant="ghost" size="sm" class="font-medium">
                Batal
            </flux:button>
            
            <flux:button 
                @click="
                    if (confirmAction) {
                        $wire.call(confirmAction, payload);
                        isOpen = false;
                    }
                " 
                variant="primary" 
                color="{{ $variant === 'danger' ? 'red' : 'indigo' }}" 
                size="sm" 
                class="font-semibold shadow-sm"
            >
                Ya, Konfirmasi
            </flux:button>
        </div>
    </div>
</div>
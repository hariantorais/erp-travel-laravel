<div x-data="{ toasts: [] }" {{-- Listen ke event global browser window --}}
    x-on:toast.window="
        let id = Date.now();
        toasts.push({
            id: id,
            type: $event.detail.type || 'success',
            title: $event.detail.title || 'Pemberitahuan',
            message: $event.detail.message || '',
            show: true
        });
        // Otomatis hilangkan toast setelah 4 detik
        setTimeout(() => {
            let index = toasts.findIndex(t => t.id === id);
            if(index !== -1) toasts[index].show = false;
            // Hapus permanen dari array setelah animasi keluar selesai
            setTimeout(() => { toasts = toasts.filter(t => t.id !== id) }, 300);
        }, 4000);
    "
    class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" x-transition:enter="animate-slide-in-right" x-transition:leave="animate-fade-out-down"
            class="w-full bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-slate-100 flex items-start gap-3 pointer-events-auto relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1"
                :class="{
                    'bg-emerald-500': toast.type === 'success',
                    'bg-amber-500': toast.type === 'warning',
                    'bg-rose-500': toast.type === 'error',
                    'bg-indigo-500': toast.type === 'info'
                }">
            </div>

            <div class="p-1.5 rounded-xl shrink-0"
                :class="{
                    'bg-emerald-50 text-emerald-600': toast.type === 'success',
                    'bg-amber-50 text-amber-600': toast.type === 'warning',
                    'bg-rose-50 text-rose-600': toast.type === 'error',
                    'bg-indigo-50 text-indigo-600': toast.type === 'info'
                }">
                <template x-if="toast.type === 'success'">
                    <flux:icon name="check-circle" variant="mini" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'warning'">
                    <flux:icon name="exclamation-triangle" variant="mini" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'error'">
                    <flux:icon name="x-circle" variant="mini" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'info'">
                    <flux:icon name="information-circle" variant="mini" class="w-5 h-5" />
                </template>
            </div>

            <div class="flex-1 min-w-0 pt-0.5">
                <span x-text="toast.title" class="block text-xs font-bold text-slate-900 tracking-tight"></span>
                <p x-text="toast.message" class="text-[11px] text-slate-500 font-medium mt-0.5 leading-relaxed"></p>
            </div>

            <button type="button" @click="toast.show = false"
                class="text-slate-400 hover:text-slate-600 transition-colors pt-0.5">
                <flux:icon name="x-mark" variant="micro" class="w-4 h-4" />
            </button>
        </div>
    </template>
</div>

@props([
    'data', // Menampung variabel data paginasi (E.g. $buses, $hotels)
    'model' => 'perPage', // Nama properti Livewire binding untuk baris per halaman
])

<div
    {{ $attributes->merge(['class' => 'p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4']) }}>

    <div class="flex items-center gap-2 shrink-0">
        <span class="text-[11px] text-slate-400 font-medium whitespace-nowrap">Tampilkan baris:</span>
        <select wire:model.live="{{ $model }}"
            class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 p-1 rounded-md focus:outline-none shadow-sm cursor-pointer">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    @if (method_exists($data, 'hasPages') && $data->hasPages())
        <div class="w-full sm:w-auto flex justify-end navigation-links-wrapper">
            {{ $data->links() }}
        </div>
    @endif

</div>

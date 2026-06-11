@props([
    'target' => 'modalForm', // Nama modal yang akan ditutup ketika tombol 'Batal' diklik
    'submitLabel' => 'Simpan', // Teks untuk tombol eksekusi utama
    'cancelLabel' => 'Batal', // Teks untuk tombol batal
    'variant' => 'primary', // Varian warna tombol submit (primary, danger, dsb)
])

<div {{ $attributes->merge(['class' => 'flex justify-end gap-2 pt-3 border-t border-slate-100']) }}>

    <flux:button x-on:click="$dispatch('close-modal', { name: '{{ $target }}' })" type="button" variant="ghost"
        size="sm" class="rounded-xl font-semibold">
        {{ $cancelLabel }}
    </flux:button>

    <flux:button type="submit" :variant="$variant" size="sm" class="rounded-xl font-semibold shadow-sm px-5">
        {{ $submitLabel }}
    </flux:button>

</div>

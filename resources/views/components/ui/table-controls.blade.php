@props([
    'placeholder' => 'Cari data...', // Teks placeholder untuk input pencarian
    'buttonLabel' => 'Tambah Data Baru', // Teks untuk tombol aksi
    'modalTarget' => null, // Nama modal yang akan dibuka (jika pakai modal)
    'buttonUrl' => null, // URL/Route tujuan (jika tambah data pindah halaman)
    'showButton' => true, // Opsi untuk menyembunyikan tombol jika hanya butuh pencarian
])

<div
    {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3']) }}>

    <div class="w-full max-w-md">
        <flux:input wire:model.live.debounce.300ms="search" :placeholder="$placeholder" icon="magnifying-glass"
            size="sm" class="w-full" />
    </div>

    @if ($showButton)
        @if ($buttonUrl)
            <flux:button href="{{ $buttonUrl }}" wire:navigate variant="primary" icon="plus" size="sm"
                class="shadow-sm font-semibold shrink-0 rounded-xl">
                {{ $buttonLabel }}
            </flux:button>
        @elseif($modalTarget)
            <flux:button x-on:click="$dispatch('open-modal', { name: '{{ $modalTarget }}' })" variant="primary"
                icon="plus" size="sm" class="shadow-sm font-semibold shrink-0 rounded-xl">
                {{ $buttonLabel }}
            </flux:button>
        @endif
    @endif

</div>

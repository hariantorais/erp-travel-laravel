@props([
    'id', // ID Record (E.g. $package->id)
    'editModal' => 'modalForm', // Nama target modal form edit (jika pakai modal)
    'editUrl' => null, // URL/Route tujuan (jika mau pindah halaman penuh)
    'deleteModal' => 'confirmDelete', // Nama target modal konfirmasi hapus
    'deleteAction' => 'deleteBranch', // Nama method backend yang akan dieksekusi saat hapus
    'showEdit' => true, // Opsi menyembunyikan tombol edit
    'showDelete' => true, // Opsi menyembunyikan tombol hapus
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-1']) }}>

    @if ($showEdit)
        @if ($editUrl)
            <flux:button href="{{ $editUrl }}" wire:navigate variant="ghost" size="sm" icon="pencil-square"
                class="text-slate-400 hover:text-indigo-600 transition-colors" title="Kelola Detail (Pindah Halaman)" />
        @else
            <flux:button x-on:click="$dispatch('open-modal', { name: '{{ $editModal }}', id: {{ $id }} })"
                variant="ghost" size="sm" icon="pencil-square"
                class="text-slate-400 hover:text-indigo-600 transition-colors" title="Ubah Data" />
        @endif
    @endif

    @if ($showDelete)
        <flux:button
            x-on:click="$dispatch('open-confirm', { 
                name: '{{ $deleteModal }}', 
                action: '{{ $deleteAction }}', 
                payload: {{ $id }} 
            })"
            variant="ghost" size="sm" icon="trash" class="text-slate-400 hover:text-rose-600 transition-colors"
            title="Hapus Data" />
    @endif

</div>

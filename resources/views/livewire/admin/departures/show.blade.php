<?php

use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\DB;

state(['departure' => null]);

mount(function ($id) {
    // Tarik data ringkas jadwal keberangkatan untuk judul halaman via DB murni
    $this->departure = DB::table('departures as d')->join('packages as p', 'p.id', '=', 'd.package_id')->where('d.id', $id)->select('d.id', 'd.code', 'p.name as package_name', 'd.departure_date')->first();

    if (!$this->departure) {
        abort(404, 'Jadwal Keberangkatan Tidak Ditemukan.');
    }
});

?>

<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-slate-200 shadow-3xs">
        <div>
            <span
                class="text-xs font-mono font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md border border-indigo-100">
                {{ $departure->code }}
            </span>
            <h1 class="text-2xl font-black text-slate-900 mt-2">Workspace Logistik: {{ $departure->package_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">Tanggal Keberangkatan Fisik: 📅
                {{ \Carbon\Carbon::parse($departure->departure_date)->format('d M Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <livewire:admin.departures.flight-manager :departure-id="$departure->id" />
    </div>

</div>

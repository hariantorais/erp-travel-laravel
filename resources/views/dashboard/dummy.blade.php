<x-layouts.app>
    <div class="max-w-xl mx-auto mt-12">
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 space-y-4">
            <h1 class="text-2xl font-bold text-slate-800">Selamat Datang di Dashboard ERP</h1>
            <p class="text-sm text-slate-600">Anda berhasil login menggunakan hak akses otoritas:</p>
            <div
                class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 font-semibold rounded-md text-xs uppercase tracking-wider">
                {{ $role }}
            </div>

            <hr class="border-slate-100 my-2" />

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-rose-600 hover:underline font-medium">
                    ← Keluar dari Sistem (Logout)
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

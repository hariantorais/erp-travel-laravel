<header
    class="hidden lg:flex h-16 w-full bg-white border-b border-slate-200 items-center justify-between px-6 sticky top-0 z-20 shrink-0">
    <div class="flex items-center gap-4">
        <flux:button @click="sidebarOpen = !sidebarOpen" variant="ghost" size="sm" icon="bars-3"
            class="text-slate-400 hover:text-slate-600 transition-colors" title="Toggle Sidebar Layout" />
        <div class="h-4 w-px bg-slate-200"></div>
        <div>
            <span class="text-xs font-medium text-slate-400">Sistem Aplikasi</span>
            <span class="text-xs font-medium text-slate-400 mx-1">/</span>
            <span class="text-xs font-semibold text-slate-700">Internal Workspace</span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle"
                class="text-slate-500 hover:text-rose-600">
                Keluar Sistem
            </flux:button>
        </form>
    </div>
</header>

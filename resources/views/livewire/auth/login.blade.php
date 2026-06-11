<?php

use function Livewire\Volt\{state, rules, title, layout};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\Auth\RoleRedirectService;

// Mengatur Judul Halaman di Layout Utama
layout('components.layouts.guest');
title('Masuk ke Sistem - ERP Umroh');

// 1. Mendefinisikan State Reaktif Form
state([
    'email' => '',
    'password' => '',
    'remember' => false,
]);

// 2. Aturan Validasi Form
rules([
    'email' => 'required|email',
    'password' => 'required|string',
]);

// 3. Logika Aksi Autentikasi
$authenticate = function (RoleRedirectService $redirectService) {
    // Validasi input form terlebih dahulu
    $this->validate();

    // Membuat Kunci Unik untuk Rate Limiter berdasarkan Email & IP Address
    $throttleKey = Str::transliterate(Str::lower($this->email) . '|' . request()->ip());

    // Periksa apakah pengguna telah melewati batas percobaan login (Maksimal 5 kali)
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    // Melakukan Percobaan Otentikasi ke Database
    if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        // Catat kegagalan login ke Rate Limiter
        RateLimiter::hit($throttleKey, 60); // Lockout selama 60 detik jika limit jebol

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    // Jika sukses, bersihkan rekam jejak percobaan login pada Rate Limiter
    RateLimiter::clear($throttleKey);

    // Regenerasi session untuk mencegah serangan Session Fixation
    session()->regenerate();

    // Ambil rute tujuan dinamis berdasarkan Role menggunakan Service Pattern kita
    $targetRoute = $redirectService->getRedirectRoute();

    // Alihkan pengguna ke dashboard yang sesuai
    return redirect()->intended($targetRoute);
};

?>

<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto w-full max-w-md">
        <flux:card class="p-8 bg-white shadow-xl border border-slate-200 rounded-2xl space-y-6">

            <div class="text-center space-y-1">
                <flux:heading size="xl" level="1" class="font-extrabold text-slate-900 tracking-tight">
                    ERP Biro Travel Umroh
                </flux:heading>
                <flux:subheading class="text-sm text-slate-500">
                    Silakan masuk untuk mengelola operasional & keuangan
                </flux:subheading>
            </div>

            <hr class="border-slate-100" />

            <form wire:submit="authenticate" class="space-y-4">

                <flux:input wire:model="email" type="email" label="Alamat Email" placeholder="nama@travel.com"
                    icon="envelope" required autofocus />

                <flux:input wire:model="password" type="password" label="Kata Sandi" placeholder="••••••••"
                    icon="key" required />

                <div class="flex items-center justify-between pt-1">
                    <flux:checkbox wire:model="remember" label="Ingat saya di perangkat ini"
                        class="text-xs font-medium text-slate-600" />
                </div>

                <div class="pt-2">
                    <flux:button type="submit" variant="primary"
                        class="w-full flex justify-center py-2.5 font-semibold text-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove>Masuk ke Aplikasi</span>
                        <span wire:loading>Memverifikasi Akun...</span>
                    </flux:button>
                </div>

            </form>

        </flux:card>
    </div>
</div>

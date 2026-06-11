<?php

// app/Observers/BookingDetailObserver.php
namespace App\Observers;

use App\Models\BookingDetail;
use App\Models\Booking;

class BookingDetailObserver
{
    /**
     * Menangani event setelah jamaah ditambahkan ke dalam booking.
     */
    public function created(BookingDetail $bookingDetail): void
    {
        $this->updateTotalJamaah($bookingDetail->booking_id);
    }

    /**
     * Menangani event setelah jamaah dihapus dari booking (Cancel Sebagian).
     */
    public function deleted(BookingDetail $bookingDetail): void
    {
        $this->updateTotalJamaah($bookingDetail->booking_id);
    }

    /**
     * Logika utama untuk menghitung ulang jumlah baris jamaah secara real-time.
     */
    protected function updateTotalJamaah(減int $bookingId): void
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            // Menghitung jumlah aktual detail booking yang terdaftar
            $currentCount = BookingDetail::where('booking_id', $bookingId)->count();

            // Update secara silent tanpa memicu event model berulang kali
            $booking->timestamps = false;
            $booking->update(['total_jamaah' => $currentCount]);
        }
    }
}

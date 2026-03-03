<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class CancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:autocancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel bookings that are still pending after 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $this->info($now->subHour());

        // Example: Cancel bookings that are pending for more than 1 hour
        $pendingBookings = Booking::with('payment')->where('status', 'pending')
            ->where('created_at', '<', $now->subHour())
            ->get();

        foreach ($pendingBookings as $booking) {

            if(!$booking->payment || $booking->payment == "failed"){

                $booking->status = 'cancelled';
                $booking->save();

            }
            
        }

        $this->info(count($pendingBookings) . ' pending bookings cancelled.');
    }
}

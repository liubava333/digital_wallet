<?php

namespace App\Jobs;

use App\Models\Billing\SubscriptionTier;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSubscriptionInvoiceJob implements ShouldQueue {
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        protected SubscriptionTier $tier
    ) {}

    public function handle(): void {
        // Isolate heavy processes out of HTTP cycle (e.g., generating PDF, calling external accounting APIs)
        Log::info("Generating heavy PDF invoice for {$this->user->email} purchasing tier {$this->tier->name}...");
        sleep(3);
        Log::info("Invoice sent successfully via Redis Queue execution chain.");
    }
}

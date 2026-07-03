<?php

namespace Database\Seeders;

use App\Models\Billing\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTierSeeder extends Seeder
{
    public function run(): void {
        $tiers = [
            [
                'name' => 'Basic Developer',
                'price' => 49.00,
                'duration_days' => 30,
            ],
            [
                'name' => 'Business Team',
                'price' => 149.00,
                'duration_days' => 30,
            ],
            [
                'name' => 'Corporate Enterprise',
                'price' => 499.00,
                'duration_days' => 365,
            ],
        ];

        foreach ($tiers as $tier) {
            SubscriptionTier::updateOrCreate(
                ['name' => $tier['name']], // Предотвращает дубликаты при повторном запуске
                $tier
            );
        }
    }
}

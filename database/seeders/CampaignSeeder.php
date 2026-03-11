<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Campaign::truncate();
        Schema::enableForeignKeyConstraints();
        Campaign::create([
            'timezone' => 'Europe/London',
            'name' => 'Test Campaign 1',
            'slug' => 'test-campaign-1',
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addDays(7)->endOfDay(),
        ]);

        Campaign::create([
            'timezone' => 'Europe/London',
            'name' => 'Test Campaign 2',
            'slug' => 'test-campaign-2',
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addDays(7)->endOfDay(),
        ]);
    }
}

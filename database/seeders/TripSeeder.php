<?php

namespace Database\Seeders;

use App\Enums\TripRole;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Create a sample trip shared by the test user and a partner.
     */
    public function run(): void
    {
        $owner = User::where('email', 'test@example.com')->firstOrFail();

        $partner = User::factory()->create([
            'name' => 'Test Partner',
            'email' => 'partner@example.com',
        ]);

        $trip = Trip::factory()->for($owner, 'creator')->create([
            'name' => 'Christmas in Antigua',
            'destination' => 'Antigua Guatemala',
            'departs_on' => now()->addWeeks(4),
        ]);

        $trip->users()->attach([
            $owner->id => ['role' => TripRole::Owner->value],
            $partner->id => ['role' => TripRole::Member->value],
        ]);
    }
}

<?php

namespace App\Actions;

use App\Enums\TripRole;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTrip
{
    /**
     * Create a trip and make the given user its owner.
     *
     * @param  array<string, mixed>  $data  Already validated by StoreTripRequest
     */
    public function handle(User $creator, array $data): Trip
    {
        // Both steps succeed or neither does, so a trip never exists without an owner
        return DB::transaction(function () use ($creator, $data): Trip {
            $trip = new Trip($data);
            $trip->creator()->associate($creator);
            $trip->save();

            $trip->users()->attach($creator, ['role' => TripRole::Owner->value]);

            return $trip;
        });
    }
}

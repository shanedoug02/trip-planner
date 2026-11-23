<?php

use App\Enums\TripRole;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;

test('the seeder creates a trip shared by two users', function () {
    $this->seed(DatabaseSeeder::class);

    $trip = Trip::sole();

    expect($trip->users)->toHaveCount(2)
        ->and($trip->creator->email)->toBe('test@example.com');
});

test('a trip lists its users with their roles', function () {
    $trip = Trip::factory()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $trip->users()->attach([
        $owner->id => ['role' => TripRole::Owner->value],
        $member->id => ['role' => TripRole::Member->value],
    ]);

    $roles = $trip->users->pluck('pivot.role', 'id');

    expect($roles[$owner->id])->toBe('owner')
        ->and($roles[$member->id])->toBe('member');
});

test('a user lists every trip they are on', function () {
    $user = User::factory()->create();
    $trips = Trip::factory()->count(2)->create();
    Trip::factory()->create(); // a trip the user isn't on

    $user->trips()->attach($trips);

    expect($user->trips->pluck('id')->sort()->values()->all())
        ->toBe($trips->pluck('id')->sort()->values()->all());
});

test('new members default to the member role', function () {
    $trip = Trip::factory()->create();
    $user = User::factory()->create();

    $trip->users()->attach($user);

    expect($trip->users->first()->pivot->role)->toBe(TripRole::Member->value);
});

test('deleting a user removes them from trips but keeps the trips', function () {
    $creator = User::factory()->create();
    $trip = Trip::factory()->for($creator, 'creator')->create();
    $trip->users()->attach($creator, ['role' => TripRole::Owner->value]);

    $creator->delete();

    $trip->refresh();
    expect($trip->exists)->toBeTrue()
        ->and($trip->created_by)->toBeNull()
        ->and($trip->users)->toBeEmpty();
});

test('deleting a trip removes its memberships', function () {
    $trip = Trip::factory()->create();
    $trip->users()->attach(User::factory()->create());

    $trip->delete();

    $this->assertDatabaseCount('trip_user', 0);
});

test('a user cannot be added to the same trip twice', function () {
    $trip = Trip::factory()->create();
    $user = User::factory()->create();

    $trip->users()->attach($user);
    $trip->users()->attach($user);
})->throws(QueryException::class);

test('departs_on is a date object', function () {
    $trip = Trip::factory()->create(['departs_on' => '2026-12-21']);

    expect($trip->fresh()->departs_on)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($trip->fresh()->departs_on->toDateString())->toBe('2026-12-21');
});

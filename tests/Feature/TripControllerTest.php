<?php

use App\Enums\TripRole;
use App\Models\Trip;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Create a trip with the given user on it as owner.
 */
function tripOwnedBy(User $user, array $attributes = []): Trip
{
    $trip = Trip::factory()->for($user, 'creator')->create($attributes);
    $trip->users()->attach($user, ['role' => TripRole::Owner->value]);

    return $trip;
}

test('guests are redirected to the login page', function () {
    $this->get(route('trips.index'))->assertRedirect(route('login'));
});

test('the trips list shows only my trips, soonest first', function () {
    $user = User::factory()->create();
    $later = tripOwnedBy($user, ['departs_on' => now()->addMonths(2)]);
    $sooner = tripOwnedBy($user, ['departs_on' => now()->addWeek()]);
    tripOwnedBy(User::factory()->create()); // someone else's trip

    $this->actingAs($user)
        ->get(route('trips.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('trips/Index')
            ->has('trips', 2)
            ->where('trips.0.id', $sooner->id)
            ->where('trips.1.id', $later->id)
        );
});

test('creating a trip saves it and makes me the owner', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('trips.store'), [
        'name' => 'Christmas in Antigua',
        'destination' => 'Antigua Guatemala',
        'departs_on' => now()->addMonth()->toDateString(),
    ]);

    $trip = Trip::sole();
    $response->assertRedirect(route('trips.show', $trip));

    expect($trip->name)->toBe('Christmas in Antigua')
        ->and($trip->created_by)->toBe($user->id)
        ->and($trip->users->sole()->pivot->role)->toBe(TripRole::Owner->value);
});

test('creating a trip requires a name and a departure date that is not in the past', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('trips.store'), [
            'name' => '',
            'departs_on' => now()->subDay()->toDateString(),
        ])
        ->assertSessionHasErrors(['name', 'departs_on']);

    expect(Trip::count())->toBe(0);
});

test('I can view my trip, with who is on it', function () {
    $user = User::factory()->create();
    $trip = tripOwnedBy($user);

    $this->actingAs($user)
        ->get(route('trips.show', $trip))
        ->assertInertia(fn (Assert $page) => $page
            ->component('trips/Show')
            ->where('trip.id', $trip->id)
            ->where('trip.users.0.name', $user->name)
            ->where('trip.users.0.pivot.role', 'owner')
        );
});

test('I can open the edit form for my trip', function () {
    $user = User::factory()->create();
    $trip = tripOwnedBy($user);

    $this->actingAs($user)
        ->get(route('trips.edit', $trip))
        ->assertInertia(fn (Assert $page) => $page->component('trips/Edit'));
});

test('I can update my trip', function () {
    $user = User::factory()->create();
    $trip = tripOwnedBy($user);

    $this->actingAs($user)
        ->put(route('trips.update', $trip), [
            'name' => 'New name',
            'destination' => null,
            'departs_on' => '2027-01-15',
        ])
        ->assertRedirect(route('trips.show', $trip));

    $trip->refresh();
    expect($trip->name)->toBe('New name')
        ->and($trip->destination)->toBeNull()
        ->and($trip->departs_on->toDateString())->toBe('2027-01-15');
});

test('I can delete my trip', function () {
    $user = User::factory()->create();
    $trip = tripOwnedBy($user);

    $this->actingAs($user)
        ->delete(route('trips.destroy', $trip))
        ->assertRedirect(route('trips.index'));

    expect(Trip::find($trip->id))->toBeNull();
});

test('another user\'s trip returns 404', function () {
    $trip = tripOwnedBy(User::factory()->create());
    $stranger = User::factory()->create();

    $this->actingAs($stranger);

    $this->get(route('trips.show', $trip))->assertNotFound();
    $this->get(route('trips.edit', $trip))->assertNotFound();
    $this->put(route('trips.update', $trip), [
        'name' => 'Hijacked',
        'departs_on' => '2027-01-15',
    ])->assertNotFound();
    $this->delete(route('trips.destroy', $trip))->assertNotFound();

    expect($trip->fresh()->name)->not->toBe('Hijacked');
});

<?php

namespace App\Http\Controllers;

use App\Actions\CreateTrip;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    /** List the user's trips, soonest departure first.  */
    public function index(Request $request): Response
    {
        return Inertia::render('trips/Index', [
            'trips' => $request->user()->trips()->orderBy('departs_on')->get(),
        ]);
    }

    /**
     * Show the form for creating a trip.
     */
    public function create(): Response
    {
        return Inertia::render('trips/Create');
    }

    /**
     * Save a new trip, with the current user as its owner.
     */
    public function store(StoreTripRequest $request, CreateTrip $createTrip): RedirectResponse
    {
        $trip = $createTrip->handle($request->user(), $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Trip created.')]);

        return to_route('trips.show', $trip);
    }

    /**
     * Show one trip and who's on it.
     */
    public function show(Request $request, Trip $trip): Response
    {
        $this->ensureUserIsOnTrip($request, $trip);

        return Inertia::render('trips/Show', [
            'trip' => $trip->load('users:id,name'),
        ]);
    }

    /**
     * Show the form for editing a trip.
     */
    public function edit(Request $request, Trip $trip): Response
    {
        $this->ensureUserIsOnTrip($request, $trip);

        return Inertia::render('trips/Edit', [
            'trip' => $trip,
        ]);
    }

    /**
     * Save changes to a trip.
     */
    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $this->ensureUserIsOnTrip($request, $trip);

        $trip->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Trip updated.')]);

        return to_route('trips.show', $trip);
    }

    /**
     * Delete a trip.
     */
    public function destroy(Request $request, Trip $trip): RedirectResponse
    {
        $this->ensureUserIsOnTrip($request, $trip);

        $trip->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Trip deleted.')]);

        return to_route('trips.index');
    }

    /**
     * Respond with 404 unless the user is on the trip, so other people's trips
     * look like they don't exist. Replaced by a TripPolicy in #15.
     */
    private function ensureUserIsOnTrip(Request $request, Trip $trip): void
    {
        abort_unless($request->user()->trips()->whereKey($trip->id)->exists(), 404);
    }
}

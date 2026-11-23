<?php

namespace App\Enums;

/**
 * A user's role on a trip, stored in trip_user.role.
 */
enum TripRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}

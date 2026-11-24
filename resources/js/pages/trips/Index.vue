<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import TripController from '@/actions/App/Http/Controllers/TripController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/dates';
import type { Trip } from '@/types';

// `trips` comes from TripController@index
defineProps<{
    trips: Trip[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Trips', href: TripController.index() }],
    },
});
</script>

<template>
    <Head title="Trips" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading title="Trips" description="Trips you're planning" />
            <Button as-child>
                <Link :href="TripController.create()">New trip</Link>
            </Button>
        </div>

        <p v-if="trips.length === 0" class="text-muted-foreground">
            No trips yet. Create one to get started.
        </p>

        <ul v-else class="divide-y rounded-xl border">
            <li v-for="trip in trips" :key="trip.id">
                <Link
                    :href="TripController.show(trip)"
                    class="flex items-center justify-between gap-4 p-4 hover:bg-muted/50"
                >
                    <div>
                        <p class="font-medium">{{ trip.name }}</p>
                        <p
                            v-if="trip.destination"
                            class="text-sm text-muted-foreground"
                        >
                            {{ trip.destination }}
                        </p>
                    </div>
                    <p class="text-sm whitespace-nowrap text-muted-foreground">
                        {{ formatDate(trip.departs_on) }}
                    </p>
                </Link>
            </li>
        </ul>
    </div>
</template>

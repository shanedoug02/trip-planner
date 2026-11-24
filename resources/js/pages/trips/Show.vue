<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import TripController from '@/actions/App/Http/Controllers/TripController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/dates';
import type { Trip, TripMember } from '@/types';

// `trip` comes from TripController@show, with its members loaded
const props = defineProps<{
    trip: Trip & { users: TripMember[] };
}>();

defineOptions({
    layout: (props: { trip: Trip }) => ({
        breadcrumbs: [
            { title: 'Trips', href: TripController.index() },
            { title: props.trip.name, href: TripController.show(props.trip) },
        ],
    }),
});

function confirmDelete(): boolean {
    return confirm(`Delete "${props.trip.name}"? This can't be undone.`);
}
</script>

<template>
    <Head :title="props.trip.name" />

    <div class="flex max-w-xl flex-col gap-6 p-4">
        <Heading
            :title="props.trip.name"
            :description="props.trip.destination ?? undefined"
        />

        <dl class="grid gap-4 rounded-xl border p-4">
            <div>
                <dt class="text-sm text-muted-foreground">Departs</dt>
                <dd class="font-medium">
                    {{ formatDate(props.trip.departs_on) }}
                </dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Who's going</dt>
                <dd class="mt-1 flex flex-wrap gap-2">
                    <span
                        v-for="member in props.trip.users"
                        :key="member.id"
                        class="flex items-center gap-1.5"
                    >
                        {{ member.name }}
                        <Badge
                            v-if="member.pivot.role === 'owner'"
                            variant="secondary"
                            >owner</Badge
                        >
                    </span>
                </dd>
            </div>
        </dl>

        <div class="flex gap-3">
            <Button as-child>
                <Link :href="TripController.edit(props.trip)">Edit</Link>
            </Button>
            <Button variant="destructive" as-child>
                <Link
                    :href="TripController.destroy(props.trip)"
                    as="button"
                    :on-before="confirmDelete"
                    >Delete</Link
                >
            </Button>
        </div>
    </div>
</template>

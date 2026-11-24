<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TripController from '@/actions/App/Http/Controllers/TripController';
import Heading from '@/components/Heading.vue';
import TripForm from '@/components/TripForm.vue';
import type { Trip } from '@/types';

// `trip` comes from TripController@edit
const props = defineProps<{
    trip: Trip;
}>();

defineOptions({
    layout: (props: { trip: Trip }) => ({
        breadcrumbs: [
            { title: 'Trips', href: TripController.index() },
            { title: props.trip.name, href: TripController.show(props.trip) },
            { title: 'Edit', href: TripController.edit(props.trip) },
        ],
    }),
});
</script>

<template>
    <Head :title="`Edit ${props.trip.name}`" />

    <div class="max-w-xl p-4">
        <Heading title="Edit trip" />
        <TripForm
            :action="TripController.update.form(props.trip)"
            :trip="props.trip"
            submit-label="Save changes"
        />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TripController from '@/actions/App/Http/Controllers/TripController';
import Heading from '@/components/Heading.vue';
import TripForm from '@/components/TripForm.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Trips', href: TripController.index() },
            { title: 'New trip', href: TripController.create() },
        ],
    },
});
</script>

<template>
    <Head title="New trip" />

    <div class="max-w-xl p-4">
        <Heading title="New trip" description="When are you leaving?" />
        <TripForm
            :action="TripController.store.form()"
            submit-label="Create trip"
        />
    </div>
</template>

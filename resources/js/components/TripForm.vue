<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Trip } from '@/types';
import type { RouteFormDefinition } from '@/wayfinder';

// Shared by the Create and Edit pages. `action` says where the form submits to.
defineProps<{
    action: RouteFormDefinition<'post'>;
    trip?: Trip;
    submitLabel: string;
}>();
</script>

<template>
    <Form v-bind="action" class="space-y-6" v-slot="{ errors, processing }">
        <div class="grid gap-2">
            <Label for="name">Trip name</Label>
            <Input
                id="name"
                name="name"
                :default-value="trip?.name"
                required
                placeholder="Christmas in Antigua"
            />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="destination">Destination (optional)</Label>
            <Input
                id="destination"
                name="destination"
                :default-value="trip?.destination ?? ''"
                placeholder="Antigua Guatemala"
            />
            <InputError :message="errors.destination" />
        </div>

        <div class="grid gap-2">
            <Label for="departs_on">Departure date</Label>
            <Input
                id="departs_on"
                name="departs_on"
                type="date"
                :default-value="trip?.departs_on"
                required
            />
            <InputError :message="errors.departs_on" />
        </div>

        <Button :disabled="processing">{{ submitLabel }}</Button>
    </Form>
</template>

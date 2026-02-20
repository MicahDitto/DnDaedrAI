<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    nextNumber: number;
    statusOptions: Record<string, string>;
}>();

const form = useForm({
    number: props.nextNumber,
    title: '',
    status: 'planned',
    planned_date: '',
    actual_date: '',
    plan: {
        objectives: '',
        encounters: '',
        npcs: '',
        locations: '',
    },
    notes: '',
    recap: '',
    outcomes: {
        summary: '',
        decisions: '',
        consequences: '',
    },
});

const submit = () => {
    form.post(route('campaigns.sessions.store', props.campaign.slug));
};
</script>

<template>
    <Head :title="`Plan New Session - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.sessions.index', campaign.slug)"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Plan New Session
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Info Section -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Session Details</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Number -->
                                    <div>
                                        <InputLabel for="number" value="Session Number" />
                                        <input
                                            id="number"
                                            v-model.number="form.number"
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <InputError :message="form.errors.number" class="mt-2" />
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <InputLabel for="status" value="Status" />
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required
                                        >
                                            <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.status" class="mt-2" />
                                    </div>

                                    <!-- Title -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="title" value="Session Title (Optional)" />
                                        <TextInput
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="e.g., The Dragon's Lair"
                                        />
                                        <InputError :message="form.errors.title" class="mt-2" />
                                    </div>

                                    <!-- Planned Date -->
                                    <div>
                                        <InputLabel for="planned_date" value="Planned Date" />
                                        <TextInput
                                            id="planned_date"
                                            v-model="form.planned_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.planned_date" class="mt-2" />
                                    </div>

                                    <!-- Actual Date -->
                                    <div>
                                        <InputLabel for="actual_date" value="Actual Date (if completed)" />
                                        <TextInput
                                            id="actual_date"
                                            v-model="form.actual_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError :message="form.errors.actual_date" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Planning Section -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Session Plan</h3>

                                <div class="space-y-6">
                                    <!-- Objectives -->
                                    <div>
                                        <InputLabel for="objectives" value="Objectives / Goals" />
                                        <textarea
                                            id="objectives"
                                            v-model="form.plan.objectives"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="What should happen this session? What are the goals?"
                                        />
                                    </div>

                                    <!-- Encounters -->
                                    <div>
                                        <InputLabel for="encounters" value="Encounters" />
                                        <textarea
                                            id="encounters"
                                            v-model="form.plan.encounters"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Combat encounters, social encounters, puzzles..."
                                        />
                                    </div>

                                    <!-- NPCs -->
                                    <div>
                                        <InputLabel for="npcs" value="NPCs to Feature" />
                                        <textarea
                                            id="npcs"
                                            v-model="form.plan.npcs"
                                            rows="2"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Which NPCs will appear? What are their roles?"
                                        />
                                    </div>

                                    <!-- Locations -->
                                    <div>
                                        <InputLabel for="locations" value="Locations" />
                                        <textarea
                                            id="locations"
                                            v-model="form.plan.locations"
                                            rows="2"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Where will the session take place?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- DM Notes Section -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">DM Notes</h3>

                                <div>
                                    <InputLabel for="notes" value="Preparation Notes" />
                                    <textarea
                                        id="notes"
                                        v-model="form.notes"
                                        rows="5"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Any other notes, reminders, or prep work for this session..."
                                    />
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.sessions.index', campaign.slug)"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Create Session
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps<{
    genres: Record<string, string>;
    ruleSystems: Record<string, string>;
}>();

const form = useForm({
    name: '',
    description: '',
    genre: '',
    rule_system: '5e',
    player_count: null as number | null,
});

const submit = () => {
    form.post(route('campaigns.store'));
};
</script>

<template>
    <Head title="Create Campaign" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create New Campaign
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Campaign Name -->
                            <div>
                                <InputLabel for="name" value="Campaign Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                    placeholder="e.g., The Dragon's Descent"
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description (optional)" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="A brief description of your campaign..."
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Genre -->
                            <div>
                                <InputLabel for="genre" value="Genre / Tone" />
                                <select
                                    id="genre"
                                    v-model="form.genre"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Select a genre...</option>
                                    <option v-for="(label, value) in genres" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.genre" class="mt-2" />
                            </div>

                            <!-- Rule System -->
                            <div>
                                <InputLabel for="rule_system" value="Rule System" />
                                <select
                                    id="rule_system"
                                    v-model="form.rule_system"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option v-for="(label, value) in ruleSystems" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.rule_system" class="mt-2" />
                            </div>

                            <!-- Player Count -->
                            <div>
                                <InputLabel for="player_count" value="Number of Players (optional)" />
                                <input
                                    id="player_count"
                                    v-model.number="form.player_count"
                                    type="number"
                                    min="1"
                                    max="20"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="e.g., 4"
                                />
                                <InputError :message="form.errors.player_count" class="mt-2" />
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4 pt-4">
                                <Link
                                    :href="route('campaigns.index')"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Create Campaign
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

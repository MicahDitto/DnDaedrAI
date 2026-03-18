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
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
}>();

const form = useForm({
    name: '',
    subtype: 'age',
    summary: '',
    content: {
        description: '',
        start_date: '',
        end_date: '',
        key_events: '',
        duration: '',
        significance: '',
        secrets: '',
    },
    confidence: 'canon',
    is_secret: false,
});

const submit = () => {
    form.post(route('campaigns.timelines.store', props.campaign.slug));
};
</script>

<template>
    <Head :title="`Create Timeline - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6 flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.timelines.index', campaign.slug)"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Create Timeline
                    </h2>
                </div>

                <div class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Info Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Basic Information</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="name" value="Name" />
                                        <TextInput
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            autofocus
                                            placeholder="e.g., The Age of Dragons"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Timeline Type" />
                                        <select
                                            id="subtype"
                                            v-model="form.subtype"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            required
                                        >
                                            <option v-for="(label, value) in subtypes" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.subtype" class="mt-2" />
                                    </div>

                                    <!-- Confidence -->
                                    <div>
                                        <InputLabel for="confidence" value="Confidence Level" />
                                        <select
                                            id="confidence"
                                            v-model="form.confidence"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option v-for="(label, value) in confidenceLevels" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.confidence" class="mt-2" />
                                    </div>

                                    <!-- Summary -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="summary" value="Short Summary" />
                                        <textarea
                                            id="summary"
                                            v-model="form.summary"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="A brief one-liner about this period..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Details Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Timeline Details</h3>

                                <div class="space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.content.description"
                                            rows="6"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Describe this period, era, or event in detail..."
                                        />
                                    </div>

                                    <!-- Start & End Dates -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <InputLabel for="start_date" value="Start Date" />
                                            <TextInput
                                                id="start_date"
                                                v-model="form.content.start_date"
                                                type="text"
                                                class="mt-1 block w-full"
                                                placeholder="e.g., Year 1200 of the Third Age"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel for="end_date" value="End Date" />
                                            <TextInput
                                                id="end_date"
                                                v-model="form.content.end_date"
                                                type="text"
                                                class="mt-1 block w-full"
                                                placeholder="e.g., Year 1500 of the Third Age"
                                            />
                                        </div>
                                    </div>

                                    <!-- Duration -->
                                    <div>
                                        <InputLabel for="duration" value="Duration" />
                                        <TextInput
                                            id="duration"
                                            v-model="form.content.duration"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="e.g., 300 years"
                                        />
                                    </div>

                                    <!-- Key Events -->
                                    <div>
                                        <InputLabel for="key_events" value="Key Events" />
                                        <textarea
                                            id="key_events"
                                            v-model="form.content.key_events"
                                            rows="5"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Major events and milestones during this period..."
                                        />
                                    </div>

                                    <!-- Significance -->
                                    <div>
                                        <InputLabel for="significance" value="Historical Significance" />
                                        <textarea
                                            id="significance"
                                            v-model="form.content.significance"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Why is this period important to your world's history?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- DM Secrets Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">DM Secrets</h3>

                                <div class="space-y-4">
                                    <!-- Is Secret -->
                                    <div class="flex items-center">
                                        <input
                                            id="is_secret"
                                            v-model="form.is_secret"
                                            type="checkbox"
                                            class="h-4 w-4 bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle rounded"
                                        />
                                        <label for="is_secret" class="ml-2 block text-sm text-arcane-grey">
                                            This entire timeline entry is a secret (DM eyes only)
                                        </label>
                                    </div>

                                    <!-- Secrets -->
                                    <div>
                                        <InputLabel for="secrets" value="Hidden Truths" />
                                        <textarea
                                            id="secrets"
                                            v-model="form.content.secrets"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Secret historical facts only you know..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Create Timeline
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

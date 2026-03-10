<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Place {
    id: string;
    name: string;
    subtype: string;
}

interface Religion {
    id: string;
    name: string;
    subtype: string;
}

interface LoreItem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        narrative?: string;
        origin?: string;
        variations?: string;
        truth_level?: string;
        cultural_significance?: string;
        known_by?: string;
        secrets?: string;
    };
    confidence: string;
    is_secret: boolean;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    lore: LoreItem;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
    places: Place[];
    religions: Religion[];
    currentOriginPlaceId: string | null;
    currentRelatedReligionId: string | null;
}>();

const form = useForm({
    name: props.lore.name,
    subtype: props.lore.subtype,
    summary: props.lore.summary || '',
    content: {
        narrative: props.lore.content?.narrative || '',
        origin: props.lore.content?.origin || '',
        variations: props.lore.content?.variations || '',
        truth_level: props.lore.content?.truth_level || '',
        cultural_significance: props.lore.content?.cultural_significance || '',
        known_by: props.lore.content?.known_by || '',
        secrets: props.lore.content?.secrets || '',
    },
    origin_place_id: props.currentOriginPlaceId || '',
    related_religion_id: props.currentRelatedReligionId || '',
    confidence: props.lore.confidence,
    is_secret: props.lore.is_secret,
});

const submit = () => {
    form.put(route('campaigns.lore.update', [props.campaign.slug, props.lore.slug]));
};
</script>

<template>
    <Head :title="`Edit ${lore.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6 flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.lore.show', [campaign.slug, lore.slug])"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Edit: {{ lore.name }}
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
                                            placeholder="e.g., The Legend of the First Dawn"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Lore Type" />
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
                                            placeholder="A brief one-liner about this lore..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Story Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">The Story</h3>

                                <div class="space-y-6">
                                    <!-- Narrative -->
                                    <div>
                                        <InputLabel for="narrative" value="Narrative" />
                                        <textarea
                                            id="narrative"
                                            v-model="form.content.narrative"
                                            rows="6"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="The full story or legend..."
                                        />
                                    </div>

                                    <!-- Origin -->
                                    <div>
                                        <InputLabel for="origin" value="Origin" />
                                        <textarea
                                            id="origin"
                                            v-model="form.content.origin"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Where and when did this story originate?"
                                        />
                                    </div>

                                    <!-- Variations -->
                                    <div>
                                        <InputLabel for="variations" value="Variations" />
                                        <textarea
                                            id="variations"
                                            v-model="form.content.variations"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Different versions across cultures or regions..."
                                        />
                                    </div>

                                    <!-- Cultural Significance -->
                                    <div>
                                        <InputLabel for="cultural_significance" value="Cultural Significance" />
                                        <textarea
                                            id="cultural_significance"
                                            v-model="form.content.cultural_significance"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How important is this to the cultures that know it?"
                                        />
                                    </div>

                                    <!-- Known By -->
                                    <div>
                                        <InputLabel for="known_by" value="Known By" />
                                        <textarea
                                            id="known_by"
                                            v-model="form.content.known_by"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Who commonly knows this story?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Connections Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Connections</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Origin Place -->
                                    <div>
                                        <InputLabel for="origin_place_id" value="Origin Place" />
                                        <select
                                            id="origin_place_id"
                                            v-model="form.origin_place_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="place in places" :key="place.id" :value="place.id">
                                                {{ place.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.origin_place_id" class="mt-2" />
                                    </div>

                                    <!-- Related Religion -->
                                    <div>
                                        <InputLabel for="related_religion_id" value="Related Religion" />
                                        <select
                                            id="related_religion_id"
                                            v-model="form.related_religion_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="religion in religions" :key="religion.id" :value="religion.id">
                                                {{ religion.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.related_religion_id" class="mt-2" />
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
                                            This entire lore entry is a secret (DM eyes only)
                                        </label>
                                    </div>

                                    <!-- Truth Level -->
                                    <div>
                                        <InputLabel for="truth_level" value="Truth Level (DM Reference)" />
                                        <textarea
                                            id="truth_level"
                                            v-model="form.content.truth_level"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How much of this story is actually true?"
                                        />
                                    </div>

                                    <!-- Secrets -->
                                    <div>
                                        <InputLabel for="secrets" value="Hidden Truths" />
                                        <textarea
                                            id="secrets"
                                            v-model="form.content.secrets"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What secrets are hidden within this lore?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.lore.show', [campaign.slug, lore.slug])"
                                    class="text-arcane-grey hover:text-white transition-colors"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Save Changes
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

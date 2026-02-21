<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Character {
    id: string;
    name: string;
    subtype: string;
}

interface Place {
    id: string;
    name: string;
    subtype: string;
}

interface Faction {
    id: string;
    name: string;
    subtype: string;
}

interface Plot {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        objectives?: string;
        stakes?: string;
        progress?: string;
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
    plot: Plot;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
    characters: Character[];
    places: Place[];
    factions: Faction[];
    currentInvolvedCharacterIds: string[];
    currentInvolvedPlaceIds: string[];
    currentInvolvedFactionIds: string[];
}>();

const form = useForm({
    name: props.plot.name,
    subtype: props.plot.subtype,
    summary: props.plot.summary || '',
    content: {
        description: props.plot.content?.description || '',
        objectives: props.plot.content?.objectives || '',
        stakes: props.plot.content?.stakes || '',
        progress: props.plot.content?.progress || '',
        secrets: props.plot.content?.secrets || '',
    },
    involved_character_ids: [...props.currentInvolvedCharacterIds],
    involved_place_ids: [...props.currentInvolvedPlaceIds],
    involved_faction_ids: [...props.currentInvolvedFactionIds],
    confidence: props.plot.confidence,
    is_secret: props.plot.is_secret,
});

const submit = () => {
    form.put(route('campaigns.plots.update', [props.campaign.slug, props.plot.slug]));
};

const toggleCharacter = (id: string) => {
    const index = form.involved_character_ids.indexOf(id);
    if (index === -1) {
        form.involved_character_ids.push(id);
    } else {
        form.involved_character_ids.splice(index, 1);
    }
};

const togglePlace = (id: string) => {
    const index = form.involved_place_ids.indexOf(id);
    if (index === -1) {
        form.involved_place_ids.push(id);
    } else {
        form.involved_place_ids.splice(index, 1);
    }
};

const toggleFaction = (id: string) => {
    const index = form.involved_faction_ids.indexOf(id);
    if (index === -1) {
        form.involved_faction_ids.push(id);
    } else {
        form.involved_faction_ids.splice(index, 1);
    }
};
</script>

<template>
    <Head :title="`Edit ${plot.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.plots.show', [campaign.slug, plot.slug])"
                    class="text-arcane-grey hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Edit: {{ plot.name }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto">
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
                                            placeholder="e.g., The Dragon's Hoard"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Plot Type" />
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
                                            placeholder="A brief one-liner about this plot..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Plot Details</h3>

                                <div class="space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.content.description"
                                            rows="4"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What is this plot about? What's the story?"
                                        />
                                    </div>

                                    <!-- Objectives -->
                                    <div>
                                        <InputLabel for="objectives" value="Objectives" />
                                        <textarea
                                            id="objectives"
                                            v-model="form.content.objectives"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What must be accomplished? What are the goals?"
                                        />
                                    </div>

                                    <!-- Stakes -->
                                    <div>
                                        <InputLabel for="stakes" value="Stakes" />
                                        <textarea
                                            id="stakes"
                                            v-model="form.content.stakes"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What happens if the party fails? What's at risk?"
                                        />
                                    </div>

                                    <!-- Progress -->
                                    <div>
                                        <InputLabel for="progress" value="Current Progress" />
                                        <textarea
                                            id="progress"
                                            v-model="form.content.progress"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What has happened so far? Where is the party in this plot?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Connections Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Connections</h3>

                                <!-- Involved Characters -->
                                <div class="mb-6" v-if="characters.length > 0">
                                    <InputLabel value="Involved Characters" />
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button
                                            v-for="character in characters"
                                            :key="character.id"
                                            type="button"
                                            @click="toggleCharacter(character.id)"
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition-all"
                                            :class="form.involved_character_ids.includes(character.id)
                                                ? 'bg-arcane-periwinkle/20 text-arcane-periwinkle border border-arcane-periwinkle'
                                                : 'bg-charcoal text-arcane-grey border border-charcoal hover:border-arcane-grey'"
                                        >
                                            {{ character.name }}
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="mb-6">
                                    <InputLabel value="Involved Characters" />
                                    <p class="mt-2 text-sm text-arcane-grey">No characters in this campaign yet.</p>
                                </div>

                                <!-- Involved Places -->
                                <div class="mb-6" v-if="places.length > 0">
                                    <InputLabel value="Involved Places" />
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button
                                            v-for="place in places"
                                            :key="place.id"
                                            type="button"
                                            @click="togglePlace(place.id)"
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition-all"
                                            :class="form.involved_place_ids.includes(place.id)
                                                ? 'bg-nature/20 text-nature border border-nature'
                                                : 'bg-charcoal text-arcane-grey border border-charcoal hover:border-arcane-grey'"
                                        >
                                            {{ place.name }}
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="mb-6">
                                    <InputLabel value="Involved Places" />
                                    <p class="mt-2 text-sm text-arcane-grey">No places in this campaign yet.</p>
                                </div>

                                <!-- Involved Factions -->
                                <div v-if="factions.length > 0">
                                    <InputLabel value="Involved Factions" />
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button
                                            v-for="faction in factions"
                                            :key="faction.id"
                                            type="button"
                                            @click="toggleFaction(faction.id)"
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition-all"
                                            :class="form.involved_faction_ids.includes(faction.id)
                                                ? 'bg-legendary-gold/20 text-legendary-gold border border-legendary-gold'
                                                : 'bg-charcoal text-arcane-grey border border-charcoal hover:border-arcane-grey'"
                                        >
                                            {{ faction.name }}
                                        </button>
                                    </div>
                                </div>
                                <div v-else>
                                    <InputLabel value="Involved Factions" />
                                    <p class="mt-2 text-sm text-arcane-grey">No factions in this campaign yet.</p>
                                </div>
                            </div>

                            <!-- Secrets Section -->
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
                                            This entire plot is a secret (DM eyes only)
                                        </label>
                                    </div>

                                    <!-- Secrets -->
                                    <div>
                                        <InputLabel for="secrets" value="Plot Secrets" />
                                        <textarea
                                            id="secrets"
                                            v-model="form.content.secrets"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What secrets are behind this plot? Hidden twists, true villains, unexpected revelations?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.plots.show', [campaign.slug, plot.slug])"
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

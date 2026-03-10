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

interface Faction {
    id: string;
    name: string;
    subtype: string;
}

interface MagicSystem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        source?: string;
        practitioners?: string;
        abilities?: string;
        limitations?: string;
        components?: string;
        learning?: string;
        history?: string;
        social_perception?: string;
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
    magicSystem: MagicSystem;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
    places: Place[];
    factions: Faction[];
    currentTaughtAtId: string | null;
    currentRegulatedById: string | null;
}>();

const form = useForm({
    name: props.magicSystem.name,
    subtype: props.magicSystem.subtype,
    summary: props.magicSystem.summary || '',
    content: {
        description: props.magicSystem.content?.description || '',
        source: props.magicSystem.content?.source || '',
        practitioners: props.magicSystem.content?.practitioners || '',
        abilities: props.magicSystem.content?.abilities || '',
        limitations: props.magicSystem.content?.limitations || '',
        components: props.magicSystem.content?.components || '',
        learning: props.magicSystem.content?.learning || '',
        history: props.magicSystem.content?.history || '',
        social_perception: props.magicSystem.content?.social_perception || '',
        secrets: props.magicSystem.content?.secrets || '',
    },
    taught_at_id: props.currentTaughtAtId || '',
    regulated_by_id: props.currentRegulatedById || '',
    confidence: props.magicSystem.confidence,
    is_secret: props.magicSystem.is_secret,
});

const submit = () => {
    form.put(route('campaigns.magic.update', [props.campaign.slug, props.magicSystem.slug]));
};
</script>

<template>
    <Head :title="`Edit ${magicSystem.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6 flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.magic.show', [campaign.slug, magicSystem.slug])"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Edit: {{ magicSystem.name }}
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
                                            placeholder="e.g., The Weave, Arcane Magic, Blood Sorcery"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Magic Type" />
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
                                            placeholder="A brief one-liner about this magic system..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Magic Details Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Magic Details</h3>

                                <div class="space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.content.description"
                                            rows="4"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What is this magic system? How does it work?"
                                        />
                                    </div>

                                    <!-- Source -->
                                    <div>
                                        <InputLabel for="source" value="Source of Power" />
                                        <textarea
                                            id="source"
                                            v-model="form.content.source"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Where does the power come from? The Weave, divine entities, nature..."
                                        />
                                    </div>

                                    <!-- Abilities -->
                                    <div>
                                        <InputLabel for="abilities" value="Abilities & Powers" />
                                        <textarea
                                            id="abilities"
                                            v-model="form.content.abilities"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What can this magic accomplish?"
                                        />
                                    </div>

                                    <!-- Limitations -->
                                    <div>
                                        <InputLabel for="limitations" value="Limitations & Costs" />
                                        <textarea
                                            id="limitations"
                                            v-model="form.content.limitations"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What are the restrictions? What price must be paid?"
                                        />
                                    </div>

                                    <!-- Components -->
                                    <div>
                                        <InputLabel for="components" value="Components & Requirements" />
                                        <textarea
                                            id="components"
                                            v-model="form.content.components"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Required materials, gestures, words of power..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Practice Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Practice & Society</h3>

                                <div class="space-y-6">
                                    <!-- Practitioners -->
                                    <div>
                                        <InputLabel for="practitioners" value="Practitioners" />
                                        <textarea
                                            id="practitioners"
                                            v-model="form.content.practitioners"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Who can use this magic? What are they called?"
                                        />
                                    </div>

                                    <!-- Learning -->
                                    <div>
                                        <InputLabel for="learning" value="Learning & Training" />
                                        <textarea
                                            id="learning"
                                            v-model="form.content.learning"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How is this magic taught or learned?"
                                        />
                                    </div>

                                    <!-- History -->
                                    <div>
                                        <InputLabel for="history" value="History" />
                                        <textarea
                                            id="history"
                                            v-model="form.content.history"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Origin and development of this magic..."
                                        />
                                    </div>

                                    <!-- Social Perception -->
                                    <div>
                                        <InputLabel for="social_perception" value="Social Perception" />
                                        <textarea
                                            id="social_perception"
                                            v-model="form.content.social_perception"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How does society view this magic? Revered, feared, outlawed?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Connections Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Connections</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Taught At -->
                                    <div>
                                        <InputLabel for="taught_at_id" value="Taught At" />
                                        <select
                                            id="taught_at_id"
                                            v-model="form.taught_at_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="place in places" :key="place.id" :value="place.id">
                                                {{ place.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.taught_at_id" class="mt-2" />
                                    </div>

                                    <!-- Regulated By -->
                                    <div>
                                        <InputLabel for="regulated_by_id" value="Regulated By" />
                                        <select
                                            id="regulated_by_id"
                                            v-model="form.regulated_by_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="faction in factions" :key="faction.id" :value="faction.id">
                                                {{ faction.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.regulated_by_id" class="mt-2" />
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
                                            This entire magic system is a secret (DM eyes only)
                                        </label>
                                    </div>

                                    <!-- Secrets -->
                                    <div>
                                        <InputLabel for="secrets" value="Hidden Knowledge" />
                                        <textarea
                                            id="secrets"
                                            v-model="form.content.secrets"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What secrets are hidden about this magic? True nature, forbidden applications?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.magic.show', [campaign.slug, magicSystem.slug])"
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

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

interface Character {
    id: string;
    name: string;
    subtype: string;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
    places: Place[];
    characters: Character[];
}>();

const form = useForm({
    name: '',
    subtype: 'pantheon',
    summary: '',
    content: {
        description: '',
        beliefs: '',
        practices: '',
        hierarchy: '',
        symbols: '',
        holy_sites: '',
        history: '',
        relationships: '',
        taboos: '',
        afterlife: '',
        secrets: '',
    },
    headquarters_id: '',
    founded_by_id: '',
    confidence: 'canon',
    is_secret: false,
});

const submit = () => {
    form.post(route('campaigns.religions.store', props.campaign.slug));
};
</script>

<template>
    <Head :title="`Create Religion - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6 flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.religions.index', campaign.slug)"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Create Religion
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
                                            placeholder="e.g., The Church of the Silver Flame"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Religion Type" />
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
                                            placeholder="A brief one-liner about this religion..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Beliefs & Practices Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Beliefs & Practices</h3>

                                <div class="space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.content.description"
                                            rows="4"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What is this religion? Overview of the faith..."
                                        />
                                    </div>

                                    <!-- Beliefs -->
                                    <div>
                                        <InputLabel for="beliefs" value="Core Beliefs" />
                                        <textarea
                                            id="beliefs"
                                            v-model="form.content.beliefs"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What are the core tenets and doctrines?"
                                        />
                                    </div>

                                    <!-- Practices -->
                                    <div>
                                        <InputLabel for="practices" value="Practices & Rituals" />
                                        <textarea
                                            id="practices"
                                            v-model="form.content.practices"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Rituals, ceremonies, observances..."
                                        />
                                    </div>

                                    <!-- Taboos -->
                                    <div>
                                        <InputLabel for="taboos" value="Taboos & Forbidden Practices" />
                                        <textarea
                                            id="taboos"
                                            v-model="form.content.taboos"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What is forbidden or taboo?"
                                        />
                                    </div>

                                    <!-- Afterlife -->
                                    <div>
                                        <InputLabel for="afterlife" value="Afterlife Beliefs" />
                                        <textarea
                                            id="afterlife"
                                            v-model="form.content.afterlife"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What happens after death?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Organization Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Organization</h3>

                                <div class="space-y-6">
                                    <!-- Hierarchy -->
                                    <div>
                                        <InputLabel for="hierarchy" value="Hierarchy & Structure" />
                                        <textarea
                                            id="hierarchy"
                                            v-model="form.content.hierarchy"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Organizational structure, ranks, titles..."
                                        />
                                    </div>

                                    <!-- Symbols -->
                                    <div>
                                        <InputLabel for="symbols" value="Sacred Symbols" />
                                        <textarea
                                            id="symbols"
                                            v-model="form.content.symbols"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Sacred symbols, colors, objects..."
                                        />
                                    </div>

                                    <!-- Holy Sites -->
                                    <div>
                                        <InputLabel for="holy_sites" value="Holy Sites" />
                                        <textarea
                                            id="holy_sites"
                                            v-model="form.content.holy_sites"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Important religious locations..."
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
                                            placeholder="Origin and development of the religion..."
                                        />
                                    </div>

                                    <!-- Relationships -->
                                    <div>
                                        <InputLabel for="relationships" value="Relations with Other Religions" />
                                        <textarea
                                            id="relationships"
                                            v-model="form.content.relationships"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How does this religion relate to others?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Connections Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Connections</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Headquarters -->
                                    <div>
                                        <InputLabel for="headquarters_id" value="Primary Holy Site" />
                                        <select
                                            id="headquarters_id"
                                            v-model="form.headquarters_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="place in places" :key="place.id" :value="place.id">
                                                {{ place.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.headquarters_id" class="mt-2" />
                                    </div>

                                    <!-- Founded By -->
                                    <div>
                                        <InputLabel for="founded_by_id" value="Founded By" />
                                        <select
                                            id="founded_by_id"
                                            v-model="form.founded_by_id"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option value="">None</option>
                                            <option v-for="character in characters" :key="character.id" :value="character.id">
                                                {{ character.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.founded_by_id" class="mt-2" />
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
                                            This entire religion is a secret (DM eyes only)
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
                                            placeholder="What secrets does this religion hide? True nature of the gods?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.religions.index', campaign.slug)"
                                    class="text-arcane-grey hover:text-white transition-colors"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Create Religion
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

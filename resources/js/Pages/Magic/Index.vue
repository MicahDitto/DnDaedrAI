<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface MagicSystem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    confidence: string;
    is_secret: boolean;
    created_at: string;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    magicSystems: MagicSystem[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredMagicSystems = computed(() => {
    if (!filterSubtype.value) return props.magicSystems;
    return props.magicSystems.filter(m => m.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        school: 'bg-arcane-periwinkle/20 text-arcane-periwinkle',
        source: 'bg-arcane-purple/20 text-arcane-purple',
        tradition: 'bg-legendary-gold/20 text-legendary-gold',
        discipline: 'bg-nature/20 text-nature',
        artifact_magic: 'bg-legendary-amber/20 text-legendary-amber',
        divine_magic: 'bg-arcane-lavender/20 text-arcane-lavender',
        primal_magic: 'bg-nature/30 text-nature',
        forbidden: 'bg-danger/20 text-danger-light',
    };
    return colors[subtype] || 'bg-charcoal text-arcane-grey';
};

const getConfidenceIcon = (confidence: string) => {
    const icons: Record<string, string> = {
        canon: 'text-nature',
        likely: 'text-arcane-periwinkle',
        rumor: 'text-legendary-gold',
        unknown: 'text-arcane-grey',
    };
    return icons[confidence] || icons.unknown;
};
</script>

<template>
    <Head :title="`Magic Systems - ${campaign.name}`" />

    <CampaignLayout>

        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <!-- Filters & Actions -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-arcane-grey">Filter by type:</label>
                        <select
                            v-model="filterSubtype"
                            class="bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm text-sm"
                        >
                            <option value="">All Magic</option>
                            <option v-for="(label, value) in subtypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <Link
                        :href="route('campaigns.magic.create', campaign.slug)"
                        class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-sm text-white shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Magic System
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-if="magicSystems.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-arcane-grey"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-white">No magic systems yet</h3>
                        <p class="mt-2 text-sm text-arcane-grey">
                            Define the magical traditions, schools, and sources of power in your world.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.magic.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                            >
                                Create Magic System
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- No matches for filter -->
                <div
                    v-else-if="filteredMagicSystems.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 p-8 text-center text-arcane-grey"
                >
                    No magic systems match the selected filter.
                </div>

                <!-- Magic System Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="magic in filteredMagicSystems"
                        :key="magic.id"
                        :href="route('campaigns.magic.show', [campaign.slug, magic.slug])"
                        class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 hover:shadow-glow-arcane-sm hover:border-arcane-periwinkle/30 transition-all duration-200"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-white flex items-center">
                                    {{ magic.name }}
                                    <svg
                                        v-if="magic.is_secret"
                                        class="w-4 h-4 ml-2 text-arcane-grey"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        title="Secret"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </h3>
                                <span
                                    :class="getSubtypeColor(magic.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ subtypes[magic.subtype] || magic.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="magic.summary"
                                class="text-sm text-arcane-grey mb-4 line-clamp-2"
                            >
                                {{ magic.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-arcane-grey">
                                <span :class="getConfidenceIcon(magic.confidence)">
                                    {{ magic.confidence }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

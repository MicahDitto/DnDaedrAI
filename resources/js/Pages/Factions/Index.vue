<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface Faction {
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
    factions: Faction[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredFactions = computed(() => {
    if (!filterSubtype.value) return props.factions;
    return props.factions.filter(f => f.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        guild: 'bg-blue-100 text-blue-800',
        government: 'bg-purple-100 text-purple-800',
        religious: 'bg-yellow-100 text-yellow-800',
        criminal: 'bg-red-100 text-red-800',
        military: 'bg-green-100 text-green-800',
        arcane: 'bg-indigo-100 text-indigo-800',
    };
    return colors[subtype] || 'bg-gray-100 text-gray-800';
};

const getConfidenceIcon = (confidence: string) => {
    const icons: Record<string, string> = {
        canon: 'text-green-500',
        likely: 'text-blue-500',
        rumor: 'text-yellow-500',
        unknown: 'text-gray-400',
    };
    return icons[confidence] || icons.unknown;
};
</script>

<template>
    <Head :title="`Factions - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.show', campaign.slug)"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Factions
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.factions.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    + New Faction
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <!-- Filters -->
                <div class="mb-6 flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter by type:</label>
                    <select
                        v-model="filterSubtype"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                    >
                        <option value="">All Factions</option>
                        <option v-for="(label, value) in subtypes" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="factions.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No factions yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Create guilds, governments, and organizations that shape your world.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.factions.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                            >
                                Create Faction
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- No matches for filter -->
                <div
                    v-else-if="filteredFactions.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center text-gray-500"
                >
                    No factions match the selected filter.
                </div>

                <!-- Faction Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="faction in filteredFactions"
                        :key="faction.id"
                        :href="route('campaigns.factions.show', [campaign.slug, faction.slug])"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    {{ faction.name }}
                                    <svg
                                        v-if="faction.is_secret"
                                        class="w-4 h-4 ml-2 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        title="Secret"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </h3>
                                <span
                                    :class="getSubtypeColor(faction.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ subtypes[faction.subtype] || faction.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="faction.summary"
                                class="text-sm text-gray-600 mb-4 line-clamp-2"
                            >
                                {{ faction.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span :class="getConfidenceIcon(faction.confidence)">
                                    {{ faction.confidence }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

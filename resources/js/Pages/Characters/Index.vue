<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface Character {
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
    characters: Character[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredCharacters = computed(() => {
    if (!filterSubtype.value) return props.characters;
    return props.characters.filter(c => c.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        pc: 'bg-green-100 text-green-800',
        npc: 'bg-blue-100 text-blue-800',
        villain: 'bg-red-100 text-red-800',
        ally: 'bg-purple-100 text-purple-800',
        neutral: 'bg-gray-100 text-gray-800',
    };
    return colors[subtype] || colors.neutral;
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
    <Head :title="`Characters - ${campaign.name}`" />

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
                        Characters
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.characters.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    + New Character
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
                        <option value="">All Characters</option>
                        <option v-for="(label, value) in subtypes" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="characters.length === 0"
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
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No characters yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Start building your world by creating your first character.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.characters.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                            >
                                Create Character
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Character Grid -->
                <div
                    v-else-if="filteredCharacters.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center text-gray-500"
                >
                    No characters match the selected filter.
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="character in filteredCharacters"
                        :key="character.id"
                        :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    {{ character.name }}
                                    <svg
                                        v-if="character.is_secret"
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
                                    :class="getSubtypeColor(character.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                >
                                    {{ subtypes[character.subtype] || character.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="character.summary"
                                class="text-sm text-gray-600 mb-4 line-clamp-2"
                            >
                                {{ character.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span :class="getConfidenceIcon(character.confidence)">
                                    {{ character.confidence }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

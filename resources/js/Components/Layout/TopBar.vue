<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

interface User {
    name: string;
    email: string;
}

interface SearchResult {
    id: string;
    type: string;
    subtype: string;
    name: string;
    slug: string;
    summary: string | null;
    is_secret: boolean;
    url: string;
}

interface SearchResponse {
    results: Record<string, SearchResult[]>;
    total: number;
    query: string;
}

const props = defineProps<{
    user: User;
    campaigns: Campaign[];
    currentCampaign?: Campaign;
}>();

const searchQuery = ref('');
const showCampaignDropdown = ref(false);
const showSearchResults = ref(false);
const searchResults = ref<Record<string, SearchResult[]>>({});
const isSearching = ref(false);
const totalResults = ref(0);
const searchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

const switchCampaign = (campaign: Campaign) => {
    router.visit(route('campaigns.show', campaign.slug));
    showCampaignDropdown.value = false;
};

const hasResults = computed(() => totalResults.value > 0);

const typeLabels: Record<string, string> = {
    characters: 'Characters',
    places: 'Places',
    items: 'Items',
    factions: 'Factions',
    plots: 'Plots',
    sessions: 'Sessions',
};

const typeIcons: Record<string, string> = {
    characters: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    places: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
    items: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    factions: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    plots: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    sessions: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
};

const typeColors: Record<string, string> = {
    characters: 'text-arcane-periwinkle',
    places: 'text-nature',
    items: 'text-legendary-gold',
    factions: 'text-arcane-purple',
    plots: 'text-legendary-gold',
    sessions: 'text-arcane-grey',
};

const performSearch = async () => {
    if (!props.currentCampaign || searchQuery.value.length < 2) {
        searchResults.value = {};
        totalResults.value = 0;
        showSearchResults.value = false;
        return;
    }

    isSearching.value = true;

    try {
        const response = await fetch(
            route('campaigns.search', props.currentCampaign.slug) + `?q=${encodeURIComponent(searchQuery.value)}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }
        );

        const data: SearchResponse = await response.json();
        searchResults.value = data.results;
        totalResults.value = data.total;
        showSearchResults.value = true;
    } catch (error) {
        console.error('Search error:', error);
        searchResults.value = {};
        totalResults.value = 0;
    } finally {
        isSearching.value = false;
    }
};

const debouncedSearch = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    searchTimeout.value = setTimeout(performSearch, 300);
};

watch(searchQuery, () => {
    if (searchQuery.value.length >= 2) {
        debouncedSearch();
    } else {
        searchResults.value = {};
        totalResults.value = 0;
        showSearchResults.value = false;
    }
});

const navigateToResult = (result: SearchResult) => {
    searchQuery.value = '';
    showSearchResults.value = false;
    router.visit(result.url);
};

const closeSearch = () => {
    showSearchResults.value = false;
};

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        closeSearch();
        (event.target as HTMLInputElement).blur();
    }
};

const handleBlur = () => {
    // Delay to allow click on results
    setTimeout(() => {
        showSearchResults.value = false;
    }, 200);
};

const handleFocus = () => {
    if (searchQuery.value.length >= 2 && totalResults.value > 0) {
        showSearchResults.value = true;
    }
};
</script>

<template>
    <header class="bg-gunmetal border-b border-charcoal/30 h-16 flex items-center justify-between px-4 lg:px-6">
        <!-- Left: Campaign Switcher -->
        <div class="flex items-center">
            <div class="relative">
                <button
                    @click="showCampaignDropdown = !showCampaignDropdown"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-charcoal transition-colors"
                >
                    <span class="font-medium text-white">
                        {{ currentCampaign?.name || 'Select Campaign' }}
                    </span>
                    <svg class="w-4 h-4 text-arcane-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Campaign Dropdown -->
                <div
                    v-show="showCampaignDropdown"
                    @click.away="showCampaignDropdown = false"
                    class="absolute left-0 mt-2 w-64 bg-gunmetal rounded-lg shadow-dark-lg border border-charcoal/50 py-2 z-50"
                >
                    <div class="px-3 py-2 text-xs font-semibold text-arcane-grey uppercase tracking-wider">
                        Your Campaigns
                    </div>
                    <button
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        @click="switchCampaign(campaign)"
                        class="w-full text-left px-3 py-2 text-arcane-grey hover:bg-charcoal hover:text-white transition-colors"
                        :class="{ 'bg-arcane-periwinkle/20 text-arcane-periwinkle': currentCampaign?.id === campaign.id }"
                    >
                        {{ campaign.name }}
                    </button>
                    <div class="border-t border-charcoal/50 mt-2 pt-2">
                        <Link
                            :href="route('campaigns.create')"
                            class="block px-3 py-2 text-arcane-periwinkle hover:bg-charcoal transition-colors"
                        >
                            + New Campaign
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Global Search -->
        <div class="flex-1 max-w-2xl mx-4">
            <div class="relative">
                <input
                    v-model="searchQuery"
                    @keydown="handleKeydown"
                    @focus="handleFocus"
                    @blur="handleBlur"
                    type="text"
                    :placeholder="currentCampaign ? 'Search characters, places, plots...' : 'Select a campaign to search'"
                    :disabled="!currentCampaign"
                    class="w-full px-4 py-2 pl-10 bg-charcoal border border-transparent rounded-lg text-slate-200 placeholder-slate-400 focus:bg-charcoal-light focus:border-arcane-periwinkle focus:ring-1 focus:ring-arcane-periwinkle focus:shadow-glow-arcane-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                />
                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-arcane-grey"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <!-- Loading indicator -->
                <div v-if="isSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-arcane-periwinkle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Search Results Dropdown -->
                <div
                    v-show="showSearchResults && searchQuery.length >= 2"
                    class="absolute left-0 right-0 mt-2 bg-gunmetal rounded-lg shadow-dark-lg border border-charcoal/50 overflow-hidden z-50 max-h-[70vh] overflow-y-auto"
                >
                    <!-- No results -->
                    <div v-if="!hasResults && !isSearching" class="px-4 py-6 text-center text-arcane-grey">
                        <svg class="mx-auto h-8 w-8 mb-2 text-arcane-grey/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-sm">No results found for "{{ searchQuery }}"</p>
                    </div>

                    <!-- Results grouped by type -->
                    <template v-for="(results, type) in searchResults" :key="type">
                        <div class="border-b border-charcoal/30 last:border-b-0">
                            <!-- Group header -->
                            <div class="px-3 py-2 bg-charcoal/30 flex items-center">
                                <svg
                                    class="w-4 h-4 mr-2"
                                    :class="typeColors[type]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="typeIcons[type]" />
                                </svg>
                                <span class="text-xs font-semibold text-arcane-grey uppercase tracking-wider">
                                    {{ typeLabels[type] }}
                                </span>
                                <span class="ml-auto text-xs text-arcane-grey/70">{{ results.length }}</span>
                            </div>

                            <!-- Results -->
                            <button
                                v-for="result in results"
                                :key="result.id"
                                @mousedown.prevent="navigateToResult(result)"
                                class="w-full text-left px-4 py-2 hover:bg-charcoal/50 transition-colors flex items-start"
                            >
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <span class="text-white font-medium truncate">{{ result.name }}</span>
                                        <svg
                                            v-if="result.is_secret"
                                            class="w-3 h-3 ml-1 text-arcane-grey flex-shrink-0"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            title="Secret"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <span
                                            v-if="result.subtype"
                                            class="ml-2 text-xs px-1.5 py-0.5 rounded bg-charcoal text-arcane-grey capitalize flex-shrink-0"
                                        >
                                            {{ result.subtype.replace('_', ' ') }}
                                        </span>
                                    </div>
                                    <p v-if="result.summary" class="text-sm text-arcane-grey truncate mt-0.5">
                                        {{ result.summary }}
                                    </p>
                                </div>
                            </button>
                        </div>
                    </template>

                    <!-- Footer with total count -->
                    <div v-if="hasResults" class="px-3 py-2 bg-charcoal/20 text-xs text-arcane-grey text-center border-t border-charcoal/30">
                        {{ totalResults }} result{{ totalResults !== 1 ? 's' : '' }} found
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: User Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications (placeholder) -->
            <button class="p-2 text-arcane-grey hover:text-arcane-periwinkle transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- User Dropdown -->
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-charcoal transition-colors">
                        <div class="w-8 h-8 bg-arcane-flow rounded-full flex items-center justify-center shadow-glow-arcane-sm">
                            <span class="text-white text-sm font-medium">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-arcane-grey" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </template>

                <template #content>
                    <div class="px-4 py-2 border-b border-charcoal/50">
                        <div class="font-medium text-white">{{ user.name }}</div>
                        <div class="text-sm text-arcane-grey">{{ user.email }}</div>
                    </div>
                    <DropdownLink :href="route('profile.edit')">
                        Profile
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
    </header>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface Place {
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
    places: Place[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredPlaces = computed(() => {
    if (!filterSubtype.value) return props.places;
    return props.places.filter(p => p.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        world: 'bg-purple-100 text-purple-800',
        continent: 'bg-indigo-100 text-indigo-800',
        region: 'bg-blue-100 text-blue-800',
        city: 'bg-green-100 text-green-800',
        town: 'bg-teal-100 text-teal-800',
        village: 'bg-cyan-100 text-cyan-800',
        dungeon: 'bg-red-100 text-red-800',
        building: 'bg-orange-100 text-orange-800',
        landmark: 'bg-yellow-100 text-yellow-800',
    };
    return colors[subtype] || 'bg-gray-100 text-gray-800';
};

const getSubtypeIcon = (subtype: string) => {
    const icons: Record<string, string> = {
        world: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        city: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        dungeon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    };
    return icons[subtype] || icons.city;
};
</script>

<template>
    <Head :title="`Places - ${campaign.name}`" />

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
                        Places
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.places.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    + New Place
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
                        <option value="">All Places</option>
                        <option v-for="(label, value) in subtypes" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="places.length === 0"
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
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No places yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Start building your world by creating your first location.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.places.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                            >
                                Create Place
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div
                    v-else-if="filteredPlaces.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center text-gray-500"
                >
                    No places match the selected filter.
                </div>

                <!-- Places Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="place in filteredPlaces"
                        :key="place.id"
                        :href="route('campaigns.places.show', [campaign.slug, place.slug])"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    {{ place.name }}
                                    <svg
                                        v-if="place.is_secret"
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
                                    :class="getSubtypeColor(place.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                >
                                    {{ subtypes[place.subtype] || place.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="place.summary"
                                class="text-sm text-gray-600 mb-4 line-clamp-2"
                            >
                                {{ place.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="capitalize">{{ place.confidence }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

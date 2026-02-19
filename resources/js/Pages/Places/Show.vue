<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { ref } from 'vue';

interface Edge {
    id: number;
    type: string;
    label: string | null;
    target_node?: {
        id: string;
        name: string;
        slug: string;
        type: string;
    };
    source_node?: {
        id: string;
        name: string;
        slug: string;
        type: string;
    };
}

interface Place {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        population?: string;
        culture?: string;
        history?: string;
        points_of_interest?: string;
        secrets?: string;
    };
    confidence: string;
    is_secret: boolean;
    created_at: string;
    updated_at: string;
    outgoing_edges: Edge[];
    incoming_edges: Edge[];
}

interface ChildPlace {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Character {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    place: Place;
    childPlaces: ChildPlace[];
    characters: Character[];
}>();

const showDeleteModal = ref(false);

const deletePlace = () => {
    router.delete(route('campaigns.places.destroy', [props.campaign.slug, props.place.slug]));
};

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

const getSubtypeLabel = (subtype: string) => {
    const labels: Record<string, string> = {
        world: 'World',
        continent: 'Continent',
        region: 'Region',
        city: 'City',
        town: 'Town',
        village: 'Village',
        dungeon: 'Dungeon',
        building: 'Building',
        landmark: 'Landmark',
    };
    return labels[subtype] || subtype;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getNodeRoute = (node: { type: string; slug: string }) => {
    const typeRoutes: Record<string, string> = {
        character: 'campaigns.characters.show',
        place: 'campaigns.places.show',
    };
    const routeName = typeRoutes[node.type];
    if (routeName) {
        return route(routeName, [props.campaign.slug, node.slug]);
    }
    return '#';
};

// Get parent place from outgoing edges
const parentPlace = props.place.outgoing_edges.find(e => e.type === 'located_in')?.target_node;
</script>

<template>
    <Head :title="`${place.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.places.index', campaign.slug)"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                            {{ place.name }}
                            <svg
                                v-if="place.is_secret"
                                class="w-5 h-5 ml-2 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                title="Secret Place"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </h2>
                        <div class="flex items-center space-x-2 mt-1">
                            <span
                                :class="getSubtypeColor(place.subtype)"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                            >
                                {{ getSubtypeLabel(place.subtype) }}
                            </span>
                            <span v-if="parentPlace" class="text-sm text-gray-500">
                                in
                                <Link
                                    :href="getNodeRoute(parentPlace)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    {{ parentPlace.name }}
                                </Link>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('campaigns.places.edit', [campaign.slug, place.slug])"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                    >
                        Edit
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Summary -->
                        <div v-if="place.summary" class="bg-white shadow-sm rounded-lg p-6">
                            <p class="text-gray-600 italic">{{ place.summary }}</p>
                        </div>

                        <!-- Description -->
                        <div v-if="place.content?.description" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ place.content.description }}</p>
                        </div>

                        <!-- Culture -->
                        <div v-if="place.content?.culture" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Culture & Society</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ place.content.culture }}</p>
                        </div>

                        <!-- History -->
                        <div v-if="place.content?.history" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">History</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ place.content.history }}</p>
                        </div>

                        <!-- Points of Interest -->
                        <div v-if="place.content?.points_of_interest" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Points of Interest</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ place.content.points_of_interest }}</p>
                        </div>

                        <!-- DM Secrets -->
                        <div v-if="place.content?.secrets" class="bg-red-50 border border-red-200 shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-red-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                DM Secrets
                            </h3>
                            <p class="text-red-800 whitespace-pre-wrap">{{ place.content.secrets }}</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Metadata -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Details</h3>
                            <dl class="space-y-3">
                                <div v-if="place.content?.population">
                                    <dt class="text-xs text-gray-500">Population</dt>
                                    <dd class="text-sm text-gray-900">{{ place.content.population }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Confidence</dt>
                                    <dd class="text-sm text-gray-900 capitalize">{{ place.confidence }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(place.created_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(place.updated_at) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Child Places -->
                        <div v-if="childPlaces.length > 0" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Locations Within</h3>
                            <ul class="space-y-2">
                                <li v-for="child in childPlaces" :key="child.id">
                                    <Link
                                        :href="route('campaigns.places.show', [campaign.slug, child.slug])"
                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                    >
                                        {{ child.name }}
                                    </Link>
                                    <span
                                        :class="getSubtypeColor(child.subtype)"
                                        class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
                                    >
                                        {{ getSubtypeLabel(child.subtype) }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Characters Here -->
                        <div v-if="characters.length > 0" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Characters Here</h3>
                            <ul class="space-y-2">
                                <li v-for="character in characters" :key="character.id">
                                    <Link
                                        :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                    >
                                        {{ character.name }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDeleteModal = false"></div>
                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Place</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Are you sure you want to delete "{{ place.name }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deletePlace"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { ref } from 'vue';

interface Character {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Place {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Item {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        properties?: string;
        history?: string;
        secrets?: string;
    };
    confidence: string;
    is_secret: boolean;
    created_at: string;
    updated_at: string;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    item: Item;
    owner: Character | null;
    location: Place | null;
}>();

const showDeleteModal = ref(false);

const deleteItem = () => {
    router.delete(route('campaigns.items.destroy', [props.campaign.slug, props.item.slug]));
};

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        weapon: 'bg-red-100 text-red-800',
        armor: 'bg-blue-100 text-blue-800',
        artifact: 'bg-purple-100 text-purple-800',
        consumable: 'bg-green-100 text-green-800',
        treasure: 'bg-yellow-100 text-yellow-800',
        mundane: 'bg-gray-100 text-gray-800',
    };
    return colors[subtype] || 'bg-gray-100 text-gray-800';
};

const getSubtypeLabel = (subtype: string) => {
    const labels: Record<string, string> = {
        weapon: 'Weapon',
        armor: 'Armor',
        artifact: 'Artifact',
        consumable: 'Consumable',
        treasure: 'Treasure',
        mundane: 'Mundane',
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
</script>

<template>
    <Head :title="`${item.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.items.index', campaign.slug)"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                            {{ item.name }}
                            <svg
                                v-if="item.is_secret"
                                class="w-5 h-5 ml-2 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                title="Secret Item"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </h2>
                        <span
                            :class="getSubtypeColor(item.subtype)"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1"
                        >
                            {{ getSubtypeLabel(item.subtype) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('campaigns.items.edit', [campaign.slug, item.slug])"
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
                        <div v-if="item.summary" class="bg-white shadow-sm rounded-lg p-6">
                            <p class="text-gray-600 italic">{{ item.summary }}</p>
                        </div>

                        <!-- Description -->
                        <div v-if="item.content?.description" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ item.content.description }}</p>
                        </div>

                        <!-- Properties -->
                        <div v-if="item.content?.properties" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Properties & Abilities</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ item.content.properties }}</p>
                        </div>

                        <!-- History -->
                        <div v-if="item.content?.history" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">History & Lore</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ item.content.history }}</p>
                        </div>

                        <!-- DM Secrets -->
                        <div v-if="item.content?.secrets" class="bg-red-50 border border-red-200 shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-red-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                DM Secrets
                            </h3>
                            <p class="text-red-800 whitespace-pre-wrap">{{ item.content.secrets }}</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Metadata -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-xs text-gray-500">Confidence</dt>
                                    <dd class="text-sm text-gray-900 capitalize">{{ item.confidence }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(item.created_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(item.updated_at) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Owner -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Owner</h3>
                            <div v-if="owner">
                                <Link
                                    :href="route('campaigns.characters.show', [campaign.slug, owner.slug])"
                                    class="flex items-center text-indigo-600 hover:text-indigo-900"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ owner.name }}
                                </Link>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                No owner
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Location</h3>
                            <div v-if="location">
                                <Link
                                    :href="route('campaigns.places.show', [campaign.slug, location.slug])"
                                    class="flex items-center text-indigo-600 hover:text-indigo-900"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ location.name }}
                                </Link>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                Unknown location
                            </div>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Item</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Are you sure you want to delete "{{ item.name }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteItem"
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

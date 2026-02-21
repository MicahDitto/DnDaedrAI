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

interface RelatedFaction {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Faction {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        goals?: string;
        methods?: string;
        resources?: string;
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
    faction: Faction;
    members: Character[];
    headquarters: Place | null;
    alliedFactions: RelatedFaction[];
    rivalFactions: RelatedFaction[];
}>();

const showDeleteModal = ref(false);

const deleteFaction = () => {
    router.delete(route('campaigns.factions.destroy', [props.campaign.slug, props.faction.slug]));
};

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        guild: 'bg-arcane-periwinkle/20 text-arcane-periwinkle',
        government: 'bg-arcane-purple/20 text-arcane-purple',
        religious: 'bg-legendary-gold/20 text-legendary-gold',
        criminal: 'bg-danger/20 text-danger-light',
        military: 'bg-nature/20 text-nature',
        arcane: 'bg-arcane-periwinkle/30 text-arcane-periwinkle',
    };
    return colors[subtype] || 'bg-charcoal text-arcane-grey';
};

const getSubtypeLabel = (subtype: string) => {
    const labels: Record<string, string> = {
        guild: 'Guild',
        government: 'Government',
        religious: 'Religious Order',
        criminal: 'Criminal Organization',
        military: 'Military',
        arcane: 'Arcane Order',
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
    <Head :title="`${faction.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.factions.index', campaign.slug)"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
                            {{ faction.name }}
                            <svg
                                v-if="faction.is_secret"
                                class="w-5 h-5 ml-2 text-arcane-grey"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                title="Secret Faction"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </h2>
                        <span
                            :class="getSubtypeColor(faction.subtype)"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1"
                        >
                            {{ getSubtypeLabel(faction.subtype) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('campaigns.factions.edit', [campaign.slug, faction.slug])"
                        class="inline-flex items-center px-4 py-2 bg-gunmetal border border-charcoal rounded-md font-semibold text-xs text-arcane-grey uppercase tracking-widest shadow-dark-sm hover:bg-charcoal hover:text-white transition-colors"
                    >
                        Edit
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all"
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
                        <div v-if="faction.summary" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <p class="text-arcane-grey italic">{{ faction.summary }}</p>
                        </div>

                        <!-- Description -->
                        <div v-if="faction.content?.description" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-lg font-medium text-white mb-3">Description</h3>
                            <p class="text-arcane-grey whitespace-pre-wrap">{{ faction.content.description }}</p>
                        </div>

                        <!-- Goals -->
                        <div v-if="faction.content?.goals" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-lg font-medium text-white mb-3">Goals & Objectives</h3>
                            <p class="text-arcane-grey whitespace-pre-wrap">{{ faction.content.goals }}</p>
                        </div>

                        <!-- Methods -->
                        <div v-if="faction.content?.methods" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-lg font-medium text-white mb-3">Methods & Operations</h3>
                            <p class="text-arcane-grey whitespace-pre-wrap">{{ faction.content.methods }}</p>
                        </div>

                        <!-- Resources -->
                        <div v-if="faction.content?.resources" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-lg font-medium text-white mb-3">Resources & Assets</h3>
                            <p class="text-arcane-grey whitespace-pre-wrap">{{ faction.content.resources }}</p>
                        </div>

                        <!-- History -->
                        <div v-if="faction.content?.history" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-lg font-medium text-white mb-3">History</h3>
                            <p class="text-arcane-grey whitespace-pre-wrap">{{ faction.content.history }}</p>
                        </div>

                        <!-- DM Secrets -->
                        <div v-if="faction.content?.secrets" class="bg-danger/10 border border-danger/30 shadow-dark-md rounded-lg p-6">
                            <h3 class="text-lg font-medium text-danger-light mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                DM Secrets
                            </h3>
                            <p class="text-danger-light whitespace-pre-wrap">{{ faction.content.secrets }}</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Metadata -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-xs text-arcane-grey">Confidence</dt>
                                    <dd class="text-sm text-white capitalize">{{ faction.confidence }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-arcane-grey">Created</dt>
                                    <dd class="text-sm text-white">{{ formatDate(faction.created_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-arcane-grey">Updated</dt>
                                    <dd class="text-sm text-white">{{ formatDate(faction.updated_at) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Headquarters -->
                        <div v-if="headquarters" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Headquarters</h3>
                            <Link
                                :href="route('campaigns.places.show', [campaign.slug, headquarters.slug])"
                                class="flex items-center text-arcane-periwinkle hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ headquarters.name }}
                            </Link>
                        </div>

                        <!-- Members -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Members</h3>

                            <div v-if="members.length === 0" class="text-sm text-arcane-grey">
                                No known members.
                            </div>

                            <div v-else class="space-y-2">
                                <Link
                                    v-for="member in members"
                                    :key="member.id"
                                    :href="route('campaigns.characters.show', [campaign.slug, member.slug])"
                                    class="flex items-center text-sm text-arcane-periwinkle hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ member.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Allied Factions -->
                        <div v-if="alliedFactions.length > 0" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Allies</h3>
                            <div class="space-y-2">
                                <Link
                                    v-for="ally in alliedFactions"
                                    :key="ally.id"
                                    :href="route('campaigns.factions.show', [campaign.slug, ally.slug])"
                                    class="flex items-center text-sm text-nature hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ ally.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Rival Factions -->
                        <div v-if="rivalFactions.length > 0" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Rivals</h3>
                            <div class="space-y-2">
                                <Link
                                    v-for="rival in rivalFactions"
                                    :key="rival.id"
                                    :href="route('campaigns.factions.show', [campaign.slug, rival.slug])"
                                    class="flex items-center text-sm text-danger-light hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ rival.name }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">Delete Faction</h3>
                    <p class="text-sm text-arcane-grey mb-6">
                        Are you sure you want to delete "{{ faction.name }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteFaction"
                            class="px-4 py-2 text-sm font-medium text-white bg-danger border border-transparent rounded-md hover:shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

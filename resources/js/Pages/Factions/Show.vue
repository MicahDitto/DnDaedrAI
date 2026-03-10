<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import RelationshipManager from '@/Components/RelationshipManager.vue';
import EntityInfoCard from '@/Components/Entity/EntityInfoCard.vue';
import EntityImageCard from '@/Components/Entity/EntityImageCard.vue';
import DmControlsCard from '@/Components/Entity/DmControlsCard.vue';
import DetailSection from '@/Components/Entity/DetailSection.vue';
import DeleteConfirmModal from '@/Components/Entity/DeleteConfirmModal.vue';
import { ref } from 'vue';

interface Edge {
    id: number;
    type: string;
    label: string | null;
    strength: number | null;
    is_secret: boolean;
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
    image_url?: string | null;
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
    outgoing_edges: Edge[];
    incoming_edges: Edge[];
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
const showSecrets = ref(false);

const deleteFaction = () => {
    router.delete(route('campaigns.factions.destroy', [props.campaign.slug, props.faction.slug]));
};

const hasSecrets = !!props.faction.content?.secrets;
</script>

<template>
    <Head :title="`${faction.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="faction.image_url"
                            :entity-name="faction.name"
                            entity-type="faction"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="faction.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Headquarters -->
                        <div v-if="headquarters" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
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
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Members</h3>
                            <div v-if="members.length === 0" class="text-sm text-arcane-grey">
                                No known members
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

                        <!-- Allied & Rival Factions -->
                        <div v-if="alliedFactions.length > 0 || rivalFactions.length > 0" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Faction Relations</h3>
                            <div class="space-y-4">
                                <!-- Allies -->
                                <div v-if="alliedFactions.length > 0">
                                    <dt class="text-xs text-nature mb-2">Allies</dt>
                                    <dd class="space-y-2">
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
                                    </dd>
                                </div>
                                <!-- Rivals -->
                                <div v-if="rivalFactions.length > 0">
                                    <dt class="text-xs text-danger-light mb-2">Rivals</dt>
                                    <dd class="space-y-2">
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
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="faction.id"
                            :node-name="faction.name"
                            node-type="faction"
                            :initial-outgoing-edges="faction.outgoing_edges"
                            :initial-incoming-edges="faction.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="faction.name"
                            type="faction"
                            :subtype="faction.subtype"
                            :confidence="faction.confidence"
                            :summary="faction.summary"
                            :is-secret="faction.is_secret"
                            :edit-url="route('campaigns.factions.edit', [campaign.slug, faction.slug])"
                            :back-url="route('campaigns.factions.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="faction.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Goals & Objectives"
                            :content="faction.content?.goals"
                            icon="goals"
                        />

                        <DetailSection
                            title="Methods & Operations"
                            :content="faction.content?.methods"
                            icon="methods"
                        />

                        <DetailSection
                            title="Resources & Assets"
                            :content="faction.content?.resources"
                            icon="resources"
                        />

                        <DetailSection
                            title="History"
                            :content="faction.content?.history"
                            icon="history"
                        />

                        <!-- DM Secrets (conditionally shown) -->
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-2"
                        >
                            <DetailSection
                                v-if="showSecrets"
                                title="DM Secrets"
                                :content="faction.content?.secrets"
                                icon="secret"
                                variant="danger"
                            />
                        </Transition>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <DeleteConfirmModal
            :show="showDeleteModal"
            entity-type="faction"
            :entity-name="faction.name"
            @close="showDeleteModal = false"
            @confirm="deleteFaction"
        />
    </CampaignLayout>
</template>

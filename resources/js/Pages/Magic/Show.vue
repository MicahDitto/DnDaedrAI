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

interface Place {
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
}

interface MagicSystem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    image_url?: string | null;
    content: {
        description?: string;
        source?: string;
        practitioners?: string;
        abilities?: string;
        limitations?: string;
        components?: string;
        learning?: string;
        history?: string;
        social_perception?: string;
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
    magicSystem: MagicSystem;
    taughtAt: Place | null;
    regulatedBy: Faction | null;
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteMagicSystem = () => {
    router.delete(route('campaigns.magic.destroy', [props.campaign.slug, props.magicSystem.slug]));
};

const hasSecrets = !!props.magicSystem.content?.secrets;
</script>

<template>
    <Head :title="`${magicSystem.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="magicSystem.image_url"
                            :entity-name="magicSystem.name"
                            entity-type="magic_system"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="magicSystem.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Taught At -->
                        <div v-if="taughtAt" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Taught At</h3>
                            <Link
                                :href="route('campaigns.places.show', [campaign.slug, taughtAt.slug])"
                                class="flex items-center text-arcane-periwinkle hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ taughtAt.name }}
                            </Link>
                        </div>

                        <!-- Regulated By -->
                        <div v-if="regulatedBy" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Regulated By</h3>
                            <Link
                                :href="route('campaigns.factions.show', [campaign.slug, regulatedBy.slug])"
                                class="flex items-center text-arcane-periwinkle hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ regulatedBy.name }}
                            </Link>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="magicSystem.id"
                            :node-name="magicSystem.name"
                            node-type="magic_system"
                            :initial-outgoing-edges="magicSystem.outgoing_edges"
                            :initial-incoming-edges="magicSystem.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="magicSystem.name"
                            type="magic_system"
                            :subtype="magicSystem.subtype"
                            :confidence="magicSystem.confidence"
                            :summary="magicSystem.summary"
                            :is-secret="magicSystem.is_secret"
                            :edit-url="route('campaigns.magic.edit', [campaign.slug, magicSystem.slug])"
                            :back-url="route('campaigns.magic.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="magicSystem.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Source of Power"
                            :content="magicSystem.content?.source"
                            icon="source"
                        />

                        <DetailSection
                            title="Abilities & Powers"
                            :content="magicSystem.content?.abilities"
                            icon="abilities"
                        />

                        <DetailSection
                            title="Limitations & Costs"
                            :content="magicSystem.content?.limitations"
                            icon="limitations"
                        />

                        <DetailSection
                            title="Components & Requirements"
                            :content="magicSystem.content?.components"
                            icon="components"
                        />

                        <DetailSection
                            title="Practitioners"
                            :content="magicSystem.content?.practitioners"
                            icon="practitioners"
                        />

                        <DetailSection
                            title="Learning & Training"
                            :content="magicSystem.content?.learning"
                            icon="learning"
                        />

                        <DetailSection
                            title="History"
                            :content="magicSystem.content?.history"
                            icon="history"
                        />

                        <DetailSection
                            title="Social Perception"
                            :content="magicSystem.content?.social_perception"
                            icon="social"
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
                                title="Hidden Knowledge"
                                :content="magicSystem.content?.secrets"
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
            entity-type="magic_system"
            :entity-name="magicSystem.name"
            @close="showDeleteModal = false"
            @confirm="deleteMagicSystem"
        />
    </CampaignLayout>
</template>

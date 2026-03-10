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

interface Religion {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface LoreItem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    image_url?: string | null;
    content: {
        narrative?: string;
        origin?: string;
        variations?: string;
        truth_level?: string;
        cultural_significance?: string;
        known_by?: string;
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
    lore: LoreItem;
    originPlace: Place | null;
    relatedReligion: Religion | null;
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteLore = () => {
    router.delete(route('campaigns.lore.destroy', [props.campaign.slug, props.lore.slug]));
};

const hasSecrets = !!props.lore.content?.secrets || !!props.lore.content?.truth_level;
</script>

<template>
    <Head :title="`${lore.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="lore.image_url"
                            :entity-name="lore.name"
                            entity-type="lore"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="lore.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Origin Place -->
                        <div v-if="originPlace" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Origin</h3>
                            <Link
                                :href="route('campaigns.places.show', [campaign.slug, originPlace.slug])"
                                class="flex items-center text-arcane-periwinkle hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ originPlace.name }}
                            </Link>
                        </div>

                        <!-- Related Religion -->
                        <div v-if="relatedReligion" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Related Religion</h3>
                            <Link
                                :href="route('campaigns.religions.show', [campaign.slug, relatedReligion.slug])"
                                class="flex items-center text-legendary-gold hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                {{ relatedReligion.name }}
                            </Link>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="lore.id"
                            :node-name="lore.name"
                            node-type="lore"
                            :initial-outgoing-edges="lore.outgoing_edges"
                            :initial-incoming-edges="lore.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="lore.name"
                            type="lore"
                            :subtype="lore.subtype"
                            :confidence="lore.confidence"
                            :summary="lore.summary"
                            :is-secret="lore.is_secret"
                            :edit-url="route('campaigns.lore.edit', [campaign.slug, lore.slug])"
                            :back-url="route('campaigns.lore.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="The Story"
                            :content="lore.content?.narrative"
                            icon="narrative"
                        />

                        <DetailSection
                            title="Origin"
                            :content="lore.content?.origin"
                            icon="origin"
                        />

                        <DetailSection
                            title="Variations"
                            :content="lore.content?.variations"
                            icon="variations"
                        />

                        <DetailSection
                            title="Cultural Significance"
                            :content="lore.content?.cultural_significance"
                            icon="culture"
                        />

                        <DetailSection
                            title="Known By"
                            :content="lore.content?.known_by"
                            icon="known_by"
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
                            <div v-if="showSecrets" class="space-y-6">
                                <DetailSection
                                    title="Truth Level"
                                    :content="lore.content?.truth_level"
                                    icon="truth"
                                    variant="danger"
                                />
                                <DetailSection
                                    title="Hidden Truths"
                                    :content="lore.content?.secrets"
                                    icon="secret"
                                    variant="danger"
                                />
                            </div>
                        </Transition>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <DeleteConfirmModal
            :show="showDeleteModal"
            entity-type="lore"
            :entity-name="lore.name"
            @close="showDeleteModal = false"
            @confirm="deleteLore"
        />
    </CampaignLayout>
</template>

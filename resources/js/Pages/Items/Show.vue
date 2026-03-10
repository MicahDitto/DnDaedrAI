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

interface Item {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    image_url?: string | null;
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
    item: Item;
    owner: Character | null;
    location: Place | null;
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteItem = () => {
    router.delete(route('campaigns.items.destroy', [props.campaign.slug, props.item.slug]));
};

const hasSecrets = !!props.item.content?.secrets;
</script>

<template>
    <Head :title="`${item.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="item.image_url"
                            :entity-name="item.name"
                            entity-type="item"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="item.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Ownership Card -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Ownership</h3>
                            <div class="space-y-3">
                                <!-- Owner -->
                                <div>
                                    <dt class="text-xs text-arcane-grey mb-1">Owner</dt>
                                    <dd v-if="owner">
                                        <Link
                                            :href="route('campaigns.characters.show', [campaign.slug, owner.slug])"
                                            class="flex items-center text-arcane-periwinkle hover:text-white transition-colors text-sm"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ owner.name }}
                                        </Link>
                                    </dd>
                                    <dd v-else class="text-sm text-arcane-grey">No owner</dd>
                                </div>
                                <!-- Location -->
                                <div>
                                    <dt class="text-xs text-arcane-grey mb-1">Location</dt>
                                    <dd v-if="location">
                                        <Link
                                            :href="route('campaigns.places.show', [campaign.slug, location.slug])"
                                            class="flex items-center text-arcane-periwinkle hover:text-white transition-colors text-sm"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ location.name }}
                                        </Link>
                                    </dd>
                                    <dd v-else class="text-sm text-arcane-grey">Unknown location</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="item.id"
                            :node-name="item.name"
                            node-type="item"
                            :initial-outgoing-edges="item.outgoing_edges"
                            :initial-incoming-edges="item.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="item.name"
                            type="item"
                            :subtype="item.subtype"
                            :confidence="item.confidence"
                            :summary="item.summary"
                            :is-secret="item.is_secret"
                            :edit-url="route('campaigns.items.edit', [campaign.slug, item.slug])"
                            :back-url="route('campaigns.items.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="item.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Properties & Abilities"
                            :content="item.content?.properties"
                            icon="properties"
                        />

                        <DetailSection
                            title="History & Lore"
                            :content="item.content?.history"
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
                                :content="item.content?.secrets"
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
            entity-type="item"
            :entity-name="item.name"
            @close="showDeleteModal = false"
            @confirm="deleteItem"
        />
    </CampaignLayout>
</template>

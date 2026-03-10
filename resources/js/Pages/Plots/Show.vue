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

interface Faction {
    id: string;
    name: string;
    slug: string;
    subtype: string;
}

interface Plot {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    image_url?: string | null;
    content: {
        description?: string;
        objectives?: string;
        stakes?: string;
        progress?: string;
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
    plot: Plot;
    involvedCharacters: Character[];
    involvedPlaces: Place[];
    involvedFactions: Faction[];
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deletePlot = () => {
    router.delete(route('campaigns.plots.destroy', [props.campaign.slug, props.plot.slug]));
};

const hasSecrets = !!props.plot.content?.secrets;
</script>

<template>
    <Head :title="`${plot.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="plot.image_url"
                            :entity-name="plot.name"
                            entity-type="plot"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="plot.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Involvement Card -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Involvement</h3>
                            <div class="space-y-4">
                                <!-- Characters -->
                                <div>
                                    <dt class="text-xs text-arcane-grey mb-2">Characters</dt>
                                    <dd v-if="involvedCharacters.length > 0" class="space-y-2">
                                        <Link
                                            v-for="character in involvedCharacters"
                                            :key="character.id"
                                            :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                                            class="flex items-center text-sm text-arcane-periwinkle hover:text-white transition-colors"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ character.name }}
                                        </Link>
                                    </dd>
                                    <dd v-else class="text-sm text-arcane-grey">No characters involved</dd>
                                </div>

                                <!-- Locations -->
                                <div>
                                    <dt class="text-xs text-arcane-grey mb-2">Locations</dt>
                                    <dd v-if="involvedPlaces.length > 0" class="space-y-2">
                                        <Link
                                            v-for="place in involvedPlaces"
                                            :key="place.id"
                                            :href="route('campaigns.places.show', [campaign.slug, place.slug])"
                                            class="flex items-center text-sm text-nature hover:text-white transition-colors"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ place.name }}
                                        </Link>
                                    </dd>
                                    <dd v-else class="text-sm text-arcane-grey">No locations set</dd>
                                </div>

                                <!-- Factions -->
                                <div>
                                    <dt class="text-xs text-arcane-grey mb-2">Factions</dt>
                                    <dd v-if="involvedFactions.length > 0" class="space-y-2">
                                        <Link
                                            v-for="faction in involvedFactions"
                                            :key="faction.id"
                                            :href="route('campaigns.factions.show', [campaign.slug, faction.slug])"
                                            class="flex items-center text-sm text-legendary-gold hover:text-white transition-colors"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                            </svg>
                                            {{ faction.name }}
                                        </Link>
                                    </dd>
                                    <dd v-else class="text-sm text-arcane-grey">No factions involved</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="plot.id"
                            :node-name="plot.name"
                            node-type="plot"
                            :initial-outgoing-edges="plot.outgoing_edges"
                            :initial-incoming-edges="plot.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="plot.name"
                            type="plot"
                            :subtype="plot.subtype"
                            :confidence="plot.confidence"
                            :summary="plot.summary"
                            :is-secret="plot.is_secret"
                            :edit-url="route('campaigns.plots.edit', [campaign.slug, plot.slug])"
                            :back-url="route('campaigns.plots.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="plot.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Objectives"
                            :content="plot.content?.objectives"
                            icon="goals"
                        />

                        <DetailSection
                            title="Stakes"
                            :content="plot.content?.stakes"
                            icon="stakes"
                        />

                        <DetailSection
                            title="Current Progress"
                            :content="plot.content?.progress"
                            icon="progress"
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
                                :content="plot.content?.secrets"
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
            entity-type="plot"
            :entity-name="plot.name"
            @close="showDeleteModal = false"
            @confirm="deletePlot"
        />
    </CampaignLayout>
</template>

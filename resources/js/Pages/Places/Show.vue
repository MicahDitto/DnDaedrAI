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
    summary: string | null;
    image_url?: string | null;
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
const showSecrets = ref(false);

const deletePlace = () => {
    router.delete(route('campaigns.places.destroy', [props.campaign.slug, props.place.slug]));
};

const hasSecrets = !!props.place.content?.secrets;

// Get parent place from outgoing edges
const parentPlace = props.place.outgoing_edges.find(e => e.type === 'located_in')?.target_node;

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        world: 'bg-arcane-purple/20 text-arcane-purple',
        continent: 'bg-arcane-periwinkle/20 text-arcane-periwinkle',
        region: 'bg-arcane-periwinkle/30 text-arcane-periwinkle',
        city: 'bg-nature/20 text-nature',
        town: 'bg-nature/30 text-nature',
        village: 'bg-nature/20 text-nature',
        dungeon: 'bg-danger/20 text-danger-light',
        building: 'bg-legendary-amber/20 text-legendary-amber',
        landmark: 'bg-legendary-gold/20 text-legendary-gold',
    };
    return colors[subtype] || 'bg-charcoal text-arcane-grey';
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
</script>

<template>
    <Head :title="`${place.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="place.image_url"
                            :entity-name="place.name"
                            entity-type="place"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="place.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Hierarchy Card (Parent + Children) -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Location Hierarchy</h3>
                            <div class="space-y-4">
                                <!-- Parent Place -->
                                <div v-if="parentPlace">
                                    <dt class="text-xs text-arcane-grey mb-1">Located In</dt>
                                    <dd>
                                        <Link
                                            :href="route('campaigns.places.show', [campaign.slug, parentPlace.slug])"
                                            class="flex items-center text-arcane-periwinkle hover:text-white transition-colors text-sm"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ parentPlace.name }}
                                        </Link>
                                    </dd>
                                </div>

                                <!-- Child Places -->
                                <div v-if="childPlaces.length > 0">
                                    <dt class="text-xs text-arcane-grey mb-2">Locations Within</dt>
                                    <dd>
                                        <ul class="space-y-2">
                                            <li v-for="child in childPlaces" :key="child.id" class="flex items-center justify-between">
                                                <Link
                                                    :href="route('campaigns.places.show', [campaign.slug, child.slug])"
                                                    class="text-sm text-arcane-periwinkle hover:text-white transition-colors"
                                                >
                                                    {{ child.name }}
                                                </Link>
                                                <span
                                                    :class="getSubtypeColor(child.subtype)"
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
                                                >
                                                    {{ getSubtypeLabel(child.subtype) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </dd>
                                </div>

                                <div v-if="!parentPlace && childPlaces.length === 0" class="text-sm text-arcane-grey">
                                    No location hierarchy defined
                                </div>
                            </div>
                        </div>

                        <!-- Characters Here -->
                        <div v-if="characters.length > 0" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Characters Here</h3>
                            <ul class="space-y-2">
                                <li v-for="character in characters" :key="character.id">
                                    <Link
                                        :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                                        class="flex items-center text-sm text-arcane-periwinkle hover:text-white transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ character.name }}
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="place.id"
                            :node-name="place.name"
                            node-type="place"
                            :initial-outgoing-edges="place.outgoing_edges"
                            :initial-incoming-edges="place.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="place.name"
                            type="place"
                            :subtype="place.subtype"
                            :confidence="place.confidence"
                            :summary="place.summary"
                            :is-secret="place.is_secret"
                            :edit-url="route('campaigns.places.edit', [campaign.slug, place.slug])"
                            :back-url="route('campaigns.places.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="place.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Culture & Society"
                            :content="place.content?.culture"
                            icon="culture"
                        />

                        <DetailSection
                            title="History"
                            :content="place.content?.history"
                            icon="history"
                        />

                        <DetailSection
                            title="Points of Interest"
                            :content="place.content?.points_of_interest"
                            icon="points"
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
                                :content="place.content?.secrets"
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
            entity-type="place"
            :entity-name="place.name"
            @close="showDeleteModal = false"
            @confirm="deletePlace"
        />
    </CampaignLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import RelationshipManager from '@/Components/RelationshipManager.vue';
import EntityInfoCard from '@/Components/Entity/EntityInfoCard.vue';
import EntityImageCard from '@/Components/Entity/EntityImageCard.vue';
import DmControlsCard from '@/Components/Entity/DmControlsCard.vue';
import DetailSection from '@/Components/Entity/DetailSection.vue';
import EditableDetailSection from '@/Components/Entity/EditableDetailSection.vue';
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
    summary: string | null;
    image_url?: string | null;
    featured_image?: {
        id: string;
        url: string;
        filename: string;
    } | null;
    gallery_images?: Array<{
        id: string;
        url: string;
        filename: string;
        order: number;
    }>;
    content: {
        appearance?: string;
        personality?: string;
        motivation?: string;
        secrets?: string;
        voice_notes?: string;
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
    character: Character;
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteCharacter = () => {
    router.delete(route('campaigns.characters.destroy', [props.campaign.slug, props.character.slug]));
};

const deleteImage = (mediaId: string) => {
    if (confirm('Are you sure you want to delete this image?')) {
        router.delete(route('campaigns.characters.images.destroy', [props.campaign.slug, props.character.slug]), {
            data: { media_id: mediaId },
            preserveScroll: true,
        });
    }
};

const hasSecrets = !!props.character.content?.secrets;
</script>

<template>
    <Head :title="`${character.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Portrait/Image -->
                        <EntityImageCard
                            :image-url="character.featured_image?.url ?? character.image_url ?? null"
                            :entity-name="character.name"
                            entity-type="character"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="character.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="character.id"
                            :node-name="character.name"
                            node-type="character"
                            :initial-outgoing-edges="character.outgoing_edges"
                            :initial-incoming-edges="character.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="character.name"
                            type="character"
                            :subtype="character.subtype"
                            :confidence="character.confidence"
                            :summary="character.summary"
                            :is-secret="character.is_secret"
                            :edit-url="route('campaigns.characters.edit', [campaign.slug, character.slug])"
                            :back-url="route('campaigns.characters.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections (Inline Editable) -->
                        <EditableDetailSection
                            title="Appearance"
                            :content="character.content?.appearance"
                            icon="appearance"
                            :campaign-slug="campaign.slug"
                            :entity-slug="character.slug"
                            entity-type="character"
                            field-name="appearance"
                            :entity-data="character"
                        />

                        <EditableDetailSection
                            title="Personality"
                            :content="character.content?.personality"
                            icon="personality"
                            :campaign-slug="campaign.slug"
                            :entity-slug="character.slug"
                            entity-type="character"
                            field-name="personality"
                            :entity-data="character"
                        />

                        <EditableDetailSection
                            title="Motivation / Goals"
                            :content="character.content?.motivation"
                            icon="motivation"
                            :campaign-slug="campaign.slug"
                            :entity-slug="character.slug"
                            entity-type="character"
                            field-name="motivation"
                            :entity-data="character"
                        />

                        <EditableDetailSection
                            title="Voice / Mannerisms"
                            :content="character.content?.voice_notes"
                            icon="voice"
                            :campaign-slug="campaign.slug"
                            :entity-slug="character.slug"
                            entity-type="character"
                            field-name="voice_notes"
                            :entity-data="character"
                        />

                        <!-- Gallery -->
                        <div v-if="character.gallery_images && character.gallery_images.length > 0" class="bg-gunmetal rounded-lg shadow-dark-md border border-arcane-periwinkle/10">
                            <div class="px-6 py-4 border-b border-charcoal/50">
                                <h2 class="text-lg font-semibold text-white">Gallery</h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="image in character.gallery_images" :key="image.id" class="relative group">
                                        <img
                                            :src="image.url"
                                            :alt="image.filename"
                                            class="w-full h-48 object-cover rounded-lg border border-arcane-periwinkle/10 hover:border-arcane-purple/30 transition-colors"
                                        />
                                        <button
                                            @click="deleteImage(image.id)"
                                            class="absolute top-2 right-2 p-2 bg-danger/80 hover:bg-danger rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                :content="character.content?.secrets"
                                icon="secret"
                                variant="danger"
                            />
                        </Transition>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">Delete Character</h3>
                    <p class="text-sm text-arcane-grey mb-6">
                        Are you sure you want to delete "{{ character.name }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteCharacter"
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

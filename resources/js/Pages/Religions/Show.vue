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

interface Character {
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
}

interface Religion {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    image_url?: string | null;
    content: {
        description?: string;
        beliefs?: string;
        practices?: string;
        hierarchy?: string;
        symbols?: string;
        holy_sites?: string;
        history?: string;
        relationships?: string;
        taboos?: string;
        afterlife?: string;
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
    religion: Religion;
    headquarters: Place | null;
    founder: Character | null;
    relatedLore: LoreItem[];
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteReligion = () => {
    router.delete(route('campaigns.religions.destroy', [props.campaign.slug, props.religion.slug]));
};

const hasSecrets = !!props.religion.content?.secrets;
</script>

<template>
    <Head :title="`${religion.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <!-- Two-column grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column (4/12 = ~33%) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Image -->
                        <EntityImageCard
                            :image-url="religion.image_url"
                            :entity-name="religion.name"
                            entity-type="religion"
                        />

                        <!-- DM Controls -->
                        <DmControlsCard
                            :is-secret="religion.is_secret"
                            :has-secrets="hasSecrets"
                            @toggle-secrets-visibility="showSecrets = $event"
                        />

                        <!-- Headquarters/Holy Site -->
                        <div v-if="headquarters" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Primary Holy Site</h3>
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

                        <!-- Founder -->
                        <div v-if="founder" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Founder</h3>
                            <Link
                                :href="route('campaigns.characters.show', [campaign.slug, founder.slug])"
                                class="flex items-center text-arcane-periwinkle hover:text-white transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ founder.name }}
                            </Link>
                        </div>

                        <!-- Related Lore -->
                        <div v-if="relatedLore.length > 0" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4">Related Lore</h3>
                            <div class="space-y-2">
                                <Link
                                    v-for="lore in relatedLore"
                                    :key="lore.id"
                                    :href="route('campaigns.lore.show', [campaign.slug, lore.slug])"
                                    class="flex items-center text-sm text-arcane-periwinkle hover:text-white transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ lore.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Relationships -->
                        <RelationshipManager
                            :campaign-slug="campaign.slug"
                            :node-id="religion.id"
                            :node-name="religion.name"
                            node-type="religion"
                            :initial-outgoing-edges="religion.outgoing_edges"
                            :initial-incoming-edges="religion.incoming_edges"
                        />
                    </div>

                    <!-- Right Column (8/12 = ~67%) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Basic Info Card -->
                        <EntityInfoCard
                            :name="religion.name"
                            type="religion"
                            :subtype="religion.subtype"
                            :confidence="religion.confidence"
                            :summary="religion.summary"
                            :is-secret="religion.is_secret"
                            :edit-url="route('campaigns.religions.edit', [campaign.slug, religion.slug])"
                            :back-url="route('campaigns.religions.index', campaign.slug)"
                            @delete="showDeleteModal = true"
                        />

                        <!-- Detail Sections -->
                        <DetailSection
                            title="Description"
                            :content="religion.content?.description"
                            icon="description"
                        />

                        <DetailSection
                            title="Core Beliefs"
                            :content="religion.content?.beliefs"
                            icon="beliefs"
                        />

                        <DetailSection
                            title="Practices & Rituals"
                            :content="religion.content?.practices"
                            icon="practices"
                        />

                        <DetailSection
                            title="Hierarchy & Structure"
                            :content="religion.content?.hierarchy"
                            icon="hierarchy"
                        />

                        <DetailSection
                            title="Sacred Symbols"
                            :content="religion.content?.symbols"
                            icon="symbols"
                        />

                        <DetailSection
                            title="Holy Sites"
                            :content="religion.content?.holy_sites"
                            icon="points"
                        />

                        <DetailSection
                            title="Taboos & Forbidden Practices"
                            :content="religion.content?.taboos"
                            icon="taboos"
                        />

                        <DetailSection
                            title="Afterlife Beliefs"
                            :content="religion.content?.afterlife"
                            icon="afterlife"
                        />

                        <DetailSection
                            title="History"
                            :content="religion.content?.history"
                            icon="history"
                        />

                        <DetailSection
                            title="Relations with Other Religions"
                            :content="religion.content?.relationships"
                            icon="relationships"
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
                                :content="religion.content?.secrets"
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
            entity-type="religion"
            :entity-name="religion.name"
            @close="showDeleteModal = false"
            @confirm="deleteReligion"
        />
    </CampaignLayout>
</template>

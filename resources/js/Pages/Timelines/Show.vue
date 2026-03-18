<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import RelationshipManager from '@/Components/RelationshipManager.vue';
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

interface TimelineItem {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        description?: string;
        start_date?: string;
        end_date?: string;
        key_events?: string;
        duration?: string;
        significance?: string;
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
    timeline: TimelineItem;
}>();

const showDeleteModal = ref(false);
const showSecrets = ref(false);

const deleteTimeline = () => {
    router.delete(route('campaigns.timelines.destroy', [props.campaign.slug, props.timeline.slug]));
};

const hasSecrets = !!props.timeline.content?.secrets;
</script>

<template>
    <Head :title="`${timeline.name} - ${campaign.name}`" />

    <CampaignLayout>
        <div class="py-6">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Info Card -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-arcane-grey uppercase">Type</p>
                                    <p class="text-sm text-white capitalize">{{ timeline.subtype.replace('_', ' ') }}</p>
                                </div>
                                <div v-if="timeline.content?.start_date">
                                    <p class="text-xs text-arcane-grey uppercase">Start Date</p>
                                    <p class="text-sm text-white">{{ timeline.content.start_date }}</p>
                                </div>
                                <div v-if="timeline.content?.end_date">
                                    <p class="text-xs text-arcane-grey uppercase">End Date</p>
                                    <p class="text-sm text-white">{{ timeline.content.end_date }}</p>
                                </div>
                                <div v-if="timeline.content?.duration">
                                    <p class="text-xs text-arcane-grey uppercase">Duration</p>
                                    <p class="text-sm text-white">{{ timeline.content.duration }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-arcane-grey uppercase">Confidence</p>
                                    <p class="text-sm text-white capitalize">{{ timeline.confidence }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- DM Controls -->
                        <div v-if="timeline.is_secret || hasSecrets" class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-danger/30">
                            <h3 class="text-sm font-medium text-danger uppercase mb-4">DM Controls</h3>
                            <div class="space-y-3">
                                <div v-if="timeline.is_secret" class="flex items-center text-sm text-arcane-grey">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    Secret Entity
                                </div>
                                <button
                                    v-if="hasSecrets"
                                    @click="showSecrets = !showSecrets"
                                    class="text-sm text-danger-light hover:text-danger transition-colors"
                                >
                                    {{ showSecrets ? 'Hide' : 'Show' }} Secrets
                                </button>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
                            <div class="flex flex-col space-y-2">
                                <Link
                                    :href="route('campaigns.timelines.edit', [campaign.slug, timeline.slug])"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-glow-arcane transition-all"
                                >
                                    Edit Timeline
                                </Link>
                                <button
                                    @click="showDeleteModal = true"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-danger-dark transition-all"
                                >
                                    Delete Timeline
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white">{{ timeline.name }}</h1>
                                <p v-if="timeline.summary" class="mt-2 text-arcane-grey">{{ timeline.summary }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="timeline.content?.description" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h2 class="text-lg font-semibold text-white mb-4">Description</h2>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-arcane-grey whitespace-pre-wrap">{{ timeline.content.description }}</p>
                            </div>
                        </div>

                        <!-- Key Events -->
                        <div v-if="timeline.content?.key_events" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h2 class="text-lg font-semibold text-white mb-4">Key Events</h2>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-arcane-grey whitespace-pre-wrap">{{ timeline.content.key_events }}</p>
                            </div>
                        </div>

                        <!-- Significance -->
                        <div v-if="timeline.content?.significance" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <h2 class="text-lg font-semibold text-white mb-4">Historical Significance</h2>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-arcane-grey whitespace-pre-wrap">{{ timeline.content.significance }}</p>
                            </div>
                        </div>

                        <!-- Secrets (DM Only) -->
                        <div v-if="showSecrets && timeline.content?.secrets" class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-danger/30">
                            <h2 class="text-lg font-semibold text-danger mb-4">Hidden Truths (DM Only)</h2>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-arcane-grey whitespace-pre-wrap">{{ timeline.content.secrets }}</p>
                            </div>
                        </div>

                        <!-- Relationships -->
                        <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                            <RelationshipManager
                                :campaign-slug="campaign.slug"
                                :node-id="timeline.id"
                                :node-name="timeline.name"
                                node-type="timeline"
                                :initial-outgoing-edges="timeline.outgoing_edges"
                                :initial-incoming-edges="timeline.incoming_edges"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmModal
            :show="showDeleteModal"
            entity-type="timeline"
            :entity-name="timeline.name"
            @close="showDeleteModal = false"
            @confirm="deleteTimeline"
        />
    </CampaignLayout>
</template>

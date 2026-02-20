<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { ref } from 'vue';

interface GameSession {
    id: number;
    number: number;
    title: string | null;
    status: string;
    planned_date: string | null;
    actual_date: string | null;
    plan: {
        objectives?: string;
        encounters?: string;
        npcs?: string;
        locations?: string;
    } | null;
    notes: string | null;
    recap: string | null;
    outcomes: {
        summary?: string;
        decisions?: string;
        consequences?: string;
    } | null;
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
    session: GameSession;
    previousSession: GameSession | null;
    nextSession: GameSession | null;
    statusOptions: Record<string, string>;
}>();

const showDeleteModal = ref(false);

const deleteSession = () => {
    router.delete(route('campaigns.sessions.destroy', [props.campaign.slug, props.session.number]));
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        planned: 'bg-gray-100 text-gray-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
    };
    return colors[status] || colors.planned;
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return null;
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getSessionTitle = () => {
    if (props.session.number === 0) return 'Session 0: Campaign Setup';
    if (props.session.title) return `Session ${props.session.number}: ${props.session.title}`;
    return `Session ${props.session.number}`;
};

const hasPlanContent = () => {
    const plan = props.session.plan;
    return plan && (plan.objectives || plan.encounters || plan.npcs || plan.locations);
};

const hasOutcomes = () => {
    const outcomes = props.session.outcomes;
    return outcomes && (outcomes.summary || outcomes.decisions || outcomes.consequences);
};
</script>

<template>
    <Head :title="`${getSessionTitle()} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.sessions.index', campaign.slug)"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ getSessionTitle() }}
                        </h2>
                        <span
                            :class="getStatusColor(session.status)"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1"
                        >
                            {{ statusOptions[session.status] }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('campaigns.sessions.edit', [campaign.slug, session.number])"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                    >
                        Edit
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto">
                <!-- Session Navigation -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <Link
                            v-if="previousSession"
                            :href="route('campaigns.sessions.show', [campaign.slug, previousSession.number])"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Session {{ previousSession.number }}
                        </Link>
                    </div>
                    <div>
                        <Link
                            v-if="nextSession"
                            :href="route('campaigns.sessions.show', [campaign.slug, nextSession.number])"
                            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                        >
                            Session {{ nextSession.number }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Recap / Previously On -->
                        <div v-if="session.recap" class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-indigo-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Previously On...
                            </h3>
                            <p class="text-indigo-800 whitespace-pre-wrap italic">{{ session.recap }}</p>
                        </div>

                        <!-- Session Plan -->
                        <div v-if="hasPlanContent()" class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Session Plan
                            </h3>
                            <div class="space-y-4">
                                <div v-if="session.plan?.objectives">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Objectives</h4>
                                    <p class="text-gray-600 whitespace-pre-wrap">{{ session.plan.objectives }}</p>
                                </div>
                                <div v-if="session.plan?.encounters">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Encounters</h4>
                                    <p class="text-gray-600 whitespace-pre-wrap">{{ session.plan.encounters }}</p>
                                </div>
                                <div v-if="session.plan?.npcs">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">NPCs</h4>
                                    <p class="text-gray-600 whitespace-pre-wrap">{{ session.plan.npcs }}</p>
                                </div>
                                <div v-if="session.plan?.locations">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">Locations</h4>
                                    <p class="text-gray-600 whitespace-pre-wrap">{{ session.plan.locations }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- DM Notes -->
                        <div v-if="session.notes" class="bg-yellow-50 border border-yellow-200 shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-yellow-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                DM Notes
                            </h3>
                            <p class="text-yellow-800 whitespace-pre-wrap">{{ session.notes }}</p>
                        </div>

                        <!-- Outcomes -->
                        <div v-if="hasOutcomes()" class="bg-green-50 border border-green-200 shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-medium text-green-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Session Outcomes
                            </h3>
                            <div class="space-y-4">
                                <div v-if="session.outcomes?.summary">
                                    <h4 class="text-sm font-medium text-green-700 mb-1">Summary</h4>
                                    <p class="text-green-800 whitespace-pre-wrap">{{ session.outcomes.summary }}</p>
                                </div>
                                <div v-if="session.outcomes?.decisions">
                                    <h4 class="text-sm font-medium text-green-700 mb-1">Key Decisions</h4>
                                    <p class="text-green-800 whitespace-pre-wrap">{{ session.outcomes.decisions }}</p>
                                </div>
                                <div v-if="session.outcomes?.consequences">
                                    <h4 class="text-sm font-medium text-green-700 mb-1">Consequences</h4>
                                    <p class="text-green-800 whitespace-pre-wrap">{{ session.outcomes.consequences }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="!session.recap && !hasPlanContent() && !session.notes && !hasOutcomes()"
                            class="bg-white shadow-sm rounded-lg p-12 text-center"
                        >
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No content yet</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Add plans, notes, or outcomes to this session.
                            </p>
                            <div class="mt-6">
                                <Link
                                    :href="route('campaigns.sessions.edit', [campaign.slug, session.number])"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    Edit Session
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Session Info -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Session Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-xs text-gray-500">Session Number</dt>
                                    <dd class="text-sm text-gray-900">{{ session.number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">{{ statusOptions[session.status] }}</dd>
                                </div>
                                <div v-if="session.planned_date">
                                    <dt class="text-xs text-gray-500">Planned Date</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(session.planned_date) }}</dd>
                                </div>
                                <div v-if="session.actual_date">
                                    <dt class="text-xs text-gray-500">Actual Date</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDate(session.actual_date) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white shadow-sm rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <Link
                                    v-if="session.status === 'planned'"
                                    :href="route('campaigns.sessions.edit', [campaign.slug, session.number])"
                                    class="block w-full text-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-md text-sm font-medium hover:bg-yellow-200"
                                >
                                    Start Session
                                </Link>
                                <Link
                                    v-if="session.status === 'in_progress'"
                                    :href="route('campaigns.sessions.edit', [campaign.slug, session.number])"
                                    class="block w-full text-center px-4 py-2 bg-green-100 text-green-800 rounded-md text-sm font-medium hover:bg-green-200"
                                >
                                    Complete Session
                                </Link>
                                <Link
                                    :href="route('campaigns.sessions.create', campaign.slug)"
                                    class="block w-full text-center px-4 py-2 bg-indigo-100 text-indigo-800 rounded-md text-sm font-medium hover:bg-indigo-200"
                                >
                                    Plan Next Session
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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDeleteModal = false"></div>
                <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Session</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Are you sure you want to delete "{{ getSessionTitle() }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteSession"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

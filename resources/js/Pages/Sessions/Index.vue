<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface GameSession {
    id: number;
    number: number;
    title: string | null;
    status: string;
    planned_date: string | null;
    actual_date: string | null;
    recap: string | null;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    sessions: GameSession[];
    statusOptions: Record<string, string>;
}>();

const filterStatus = ref<string>('');

const filteredSessions = computed(() => {
    if (!filterStatus.value) return props.sessions;
    return props.sessions.filter(s => s.status === filterStatus.value);
});

const sessionZero = computed(() => props.sessions.find(s => s.number === 0));
const regularSessions = computed(() => filteredSessions.value.filter(s => s.number !== 0));

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        planned: 'bg-charcoal text-arcane-grey',
        in_progress: 'bg-legendary-gold/20 text-legendary-gold',
        completed: 'bg-nature/20 text-nature',
    };
    return colors[status] || colors.planned;
};

const formatDate = (dateStr: string | null) => {
    if (!dateStr) return null;
    return new Date(dateStr).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const getSessionTitle = (session: GameSession) => {
    if (session.number === 0) return 'Session 0: Campaign Setup';
    if (session.title) return `Session ${session.number}: ${session.title}`;
    return `Session ${session.number}`;
};
</script>

<template>
    <Head :title="`Sessions - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.show', campaign.slug)"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Sessions
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.sessions.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                >
                    + Plan New Session
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto">
                <!-- Filters -->
                <div class="mb-6 flex items-center space-x-4">
                    <label class="text-sm font-medium text-arcane-grey">Filter by status:</label>
                    <select
                        v-model="filterStatus"
                        class="bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm text-sm"
                    >
                        <option value="">All Sessions</option>
                        <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="sessions.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-arcane-grey"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-white">No sessions yet</h3>
                        <p class="mt-2 text-sm text-arcane-grey">
                            Start your campaign by planning your first session.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.sessions.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                            >
                                Plan First Session
                            </Link>
                        </div>
                    </div>
                </div>

                <template v-else>
                    <!-- Session 0 Card (if exists and not filtered out) -->
                    <div v-if="sessionZero && (!filterStatus || sessionZero.status === filterStatus)" class="mb-8">
                        <h3 class="text-sm font-medium text-arcane-grey uppercase tracking-wider mb-3">Campaign Setup</h3>
                        <Link
                            :href="route('campaigns.sessions.show', [campaign.slug, sessionZero.number])"
                            class="block bg-arcane-flow/20 border border-arcane-periwinkle/30 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-glow-arcane-sm hover:border-arcane-periwinkle/30 transition-all duration-200"
                        >
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-semibold text-white">
                                            {{ getSessionTitle(sessionZero) }}
                                        </h4>
                                        <p v-if="sessionZero.actual_date" class="text-sm text-arcane-grey mt-1">
                                            Completed {{ formatDate(sessionZero.actual_date) }}
                                        </p>
                                    </div>
                                    <span
                                        :class="getStatusColor(sessionZero.status)"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ statusOptions[sessionZero.status] }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Regular Sessions Timeline -->
                    <div v-if="regularSessions.length > 0">
                        <h3 class="text-sm font-medium text-arcane-grey uppercase tracking-wider mb-3">Session Timeline</h3>
                        <div class="space-y-4">
                            <Link
                                v-for="session in regularSessions"
                                :key="session.id"
                                :href="route('campaigns.sessions.show', [campaign.slug, session.number])"
                                class="block bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 hover:shadow-glow-arcane-sm hover:border-arcane-periwinkle/30 transition-all duration-200"
                            >
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start space-x-4">
                                            <!-- Session Number Badge -->
                                            <div class="flex-shrink-0 w-12 h-12 bg-arcane-periwinkle/20 rounded-full flex items-center justify-center">
                                                <span class="text-arcane-periwinkle font-bold text-lg">{{ session.number }}</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-white">
                                                    {{ getSessionTitle(session) }}
                                                </h4>
                                                <div class="flex items-center space-x-4 mt-1 text-sm text-arcane-grey">
                                                    <span v-if="session.actual_date">
                                                        Played {{ formatDate(session.actual_date) }}
                                                    </span>
                                                    <span v-else-if="session.planned_date">
                                                        Planned for {{ formatDate(session.planned_date) }}
                                                    </span>
                                                </div>
                                                <p
                                                    v-if="session.recap"
                                                    class="text-sm text-arcane-grey mt-2 line-clamp-2"
                                                >
                                                    {{ session.recap }}
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            :class="getStatusColor(session.status)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ statusOptions[session.status] }}
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- No matches for filter -->
                    <div
                        v-else-if="filteredSessions.length === 0"
                        class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 p-8 text-center text-arcane-grey"
                    >
                        No sessions match the selected filter.
                    </div>
                </template>
            </div>
        </div>
    </CampaignLayout>
</template>

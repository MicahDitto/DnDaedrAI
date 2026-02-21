<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    genre: string | null;
    rule_system: string;
    status: string;
    player_count: number | null;
}

interface Node {
    id: string;
    type: string;
    name: string;
    summary: string | null;
    created_at: string;
}

interface GameSession {
    id: number;
    number: number;
    title: string | null;
    status: string;
    planned_date: string | null;
}

interface Stats {
    characters: number;
    places: number;
    plots: number;
    sessions: number;
}

defineProps<{
    campaign: Campaign;
    campaigns: Campaign[];
    stats: Stats;
    recentNodes: Node[];
    sessions: GameSession[];
}>();

const getNodeIcon = (type: string) => {
    const icons: Record<string, string> = {
        character: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        place: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        item: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        faction: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        plot: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    };
    return icons[type] || icons.character;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head :title="campaign.name" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ campaign.name }}</h1>
                    <p v-if="campaign.description" class="mt-1 text-sm text-arcane-grey">
                        {{ campaign.description }}
                    </p>
                </div>
                <Link
                    :href="route('campaigns.edit', campaign.slug)"
                    class="inline-flex items-center px-3 py-2 border border-charcoal rounded-md text-sm font-medium text-arcane-grey bg-gunmetal hover:bg-charcoal hover:text-white hover:border-arcane-periwinkle/30 transition-all duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </Link>
            </div>
        </template>

        <!-- Session 0 Setup Banner -->
        <div
            v-if="campaign.status === 'setup'"
            class="mb-8 bg-arcane-flow rounded-lg shadow-glow-arcane p-6 text-white"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Complete Session 0 Setup</h2>
                    <p class="mt-1 text-arcane-lavender">
                        Answer a few questions to customize your campaign and get personalized recommendations.
                    </p>
                </div>
                <Link
                    :href="route('campaigns.setup', campaign.slug)"
                    class="px-6 py-3 bg-gunmetal text-arcane-periwinkle rounded-lg font-semibold hover:bg-charcoal hover:text-white transition-colors border border-charcoal"
                >
                    Start Setup
                </Link>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gunmetal rounded-lg shadow-dark-md p-6 border border-arcane-periwinkle/10 hover:border-arcane-purple/30 transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-arcane-purple/20 text-arcane-purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-arcane-grey">Characters</p>
                        <p class="text-2xl font-semibold text-white">{{ stats.characters }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gunmetal rounded-lg shadow-dark-md p-6 border border-arcane-periwinkle/10 hover:border-nature/30 transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-nature/20 text-nature">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-arcane-grey">Places</p>
                        <p class="text-2xl font-semibold text-white">{{ stats.places }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gunmetal rounded-lg shadow-dark-md p-6 border border-arcane-periwinkle/10 hover:border-arcane-periwinkle/30 transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-arcane-periwinkle/20 text-arcane-periwinkle">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-arcane-grey">Plots</p>
                        <p class="text-2xl font-semibold text-white">{{ stats.plots }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gunmetal rounded-lg shadow-dark-md p-6 border border-arcane-periwinkle/10 hover:border-legendary-gold/30 transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-legendary-gold/20 text-legendary-gold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-arcane-grey">Sessions</p>
                        <p class="text-2xl font-semibold text-white">{{ stats.sessions }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-gunmetal rounded-lg shadow-dark-md border border-arcane-periwinkle/10">
                <div class="px-6 py-4 border-b border-charcoal/50">
                    <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
                </div>
                <div class="divide-y divide-charcoal/30">
                    <div
                        v-if="recentNodes.length === 0"
                        class="p-6 text-center text-arcane-grey"
                    >
                        No content yet. Start by adding characters, places, or plots!
                    </div>
                    <div
                        v-for="node in recentNodes"
                        :key="node.id"
                        class="p-4 hover:bg-charcoal/30 transition-colors"
                    >
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-charcoal flex items-center justify-center">
                                    <svg class="w-5 h-5 text-arcane-periwinkle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNodeIcon(node.type)" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-white">{{ node.name }}</p>
                                <p class="text-xs text-arcane-periwinkle capitalize">{{ node.type }}</p>
                                <p v-if="node.summary" class="mt-1 text-sm text-arcane-grey line-clamp-2">
                                    {{ node.summary }}
                                </p>
                            </div>
                            <div class="text-xs text-arcane-grey/70">
                                {{ formatDate(node.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div class="bg-gunmetal rounded-lg shadow-dark-md border border-arcane-periwinkle/10">
                <div class="px-6 py-4 border-b border-charcoal/50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">Sessions</h2>
                    <button class="text-sm text-arcane-periwinkle hover:text-white font-medium transition-colors">
                        + Plan Session
                    </button>
                </div>
                <div class="divide-y divide-charcoal/30">
                    <div
                        v-if="sessions.length === 0"
                        class="p-6 text-center text-arcane-grey"
                    >
                        No sessions yet. Start planning your first session!
                    </div>
                    <div
                        v-for="session in sessions"
                        :key="session.id"
                        class="p-4 hover:bg-charcoal/30 transition-colors cursor-pointer"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-white">
                                    Session {{ session.number }}{{ session.title ? `: ${session.title}` : '' }}
                                </p>
                                <p v-if="session.planned_date" class="text-xs text-arcane-grey">
                                    {{ formatDate(session.planned_date) }}
                                </p>
                            </div>
                            <span
                                :class="{
                                    'bg-charcoal text-arcane-grey': session.status === 'planned',
                                    'bg-nature/20 text-nature': session.status === 'completed',
                                    'bg-legendary-gold/20 text-legendary-gold': session.status === 'in_progress',
                                }"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                            >
                                {{ session.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gunmetal rounded-lg shadow-dark-md p-6 border border-arcane-periwinkle/10">
            <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Link
                    :href="route('campaigns.characters.create', campaign.slug)"
                    class="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-charcoal hover:border-arcane-purple hover:bg-arcane-purple/10 transition-all duration-200 group"
                >
                    <svg class="w-8 h-8 text-arcane-grey group-hover:text-arcane-purple transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span class="mt-2 text-sm font-medium text-arcane-grey group-hover:text-white transition-colors">Add Character</span>
                </Link>

                <Link
                    :href="route('campaigns.places.create', campaign.slug)"
                    class="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-charcoal hover:border-nature hover:bg-nature/10 transition-all duration-200 group"
                >
                    <svg class="w-8 h-8 text-arcane-grey group-hover:text-nature transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="mt-2 text-sm font-medium text-arcane-grey group-hover:text-white transition-colors">Add Place</span>
                </Link>

                <button class="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-charcoal hover:border-arcane-periwinkle hover:bg-arcane-periwinkle/10 transition-all duration-200 group">
                    <svg class="w-8 h-8 text-arcane-grey group-hover:text-arcane-periwinkle transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="mt-2 text-sm font-medium text-arcane-grey group-hover:text-white transition-colors">Add Plot</span>
                </button>

                <button class="flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-charcoal hover:border-legendary-gold hover:bg-legendary-gold/10 transition-all duration-200 group">
                    <svg class="w-8 h-8 text-arcane-grey group-hover:text-legendary-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="mt-2 text-sm font-medium text-arcane-grey group-hover:text-white transition-colors">Plan Session</span>
                </button>
            </div>
        </div>
    </CampaignLayout>
</template>

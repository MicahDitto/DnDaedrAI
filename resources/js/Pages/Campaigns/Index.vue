<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    genre: string | null;
    rule_system: string;
    status: string;
    created_at: string;
}

defineProps<{
    campaigns: Campaign[];
}>();

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        setup: 'bg-yellow-100 text-yellow-800',
        active: 'bg-green-100 text-green-800',
        paused: 'bg-gray-100 text-gray-800',
        completed: 'bg-blue-100 text-blue-800',
    };
    return colors[status] || colors.setup;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="My Campaigns" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Campaigns
                </h2>
                <Link
                    :href="route('campaigns.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    + New Campaign
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div
                    v-if="campaigns.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No campaigns yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Get started by creating your first campaign.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                            >
                                Create Campaign
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Campaign Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        :href="route('campaigns.show', campaign.slug)"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ campaign.name }}
                                </h3>
                                <span
                                    :class="getStatusColor(campaign.status)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                >
                                    {{ campaign.status }}
                                </span>
                            </div>

                            <p
                                v-if="campaign.description"
                                class="text-sm text-gray-600 mb-4 line-clamp-2"
                            >
                                {{ campaign.description }}
                            </p>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <span
                                    v-if="campaign.genre"
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700"
                                >
                                    {{ campaign.genre.replace('_', ' ') }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700"
                                >
                                    {{ campaign.rule_system }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-500">
                                Created {{ formatDate(campaign.created_at) }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

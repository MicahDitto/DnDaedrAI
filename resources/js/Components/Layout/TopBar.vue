<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

interface User {
    name: string;
    email: string;
}

const props = defineProps<{
    user: User;
    campaigns: Campaign[];
    currentCampaign?: Campaign;
}>();

const searchQuery = ref('');
const showCampaignDropdown = ref(false);

const switchCampaign = (campaign: Campaign) => {
    router.visit(route('campaigns.show', campaign.slug));
    showCampaignDropdown.value = false;
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        // TODO: Implement global search
        console.log('Searching for:', searchQuery.value);
    }
};
</script>

<template>
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6">
        <!-- Left: Campaign Switcher -->
        <div class="flex items-center">
            <div class="relative">
                <button
                    @click="showCampaignDropdown = !showCampaignDropdown"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <span class="font-medium text-gray-900">
                        {{ currentCampaign?.name || 'Select Campaign' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Campaign Dropdown -->
                <div
                    v-show="showCampaignDropdown"
                    @click.away="showCampaignDropdown = false"
                    class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                >
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Your Campaigns
                    </div>
                    <button
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        @click="switchCampaign(campaign)"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100 transition-colors"
                        :class="{ 'bg-indigo-50 text-indigo-700': currentCampaign?.id === campaign.id }"
                    >
                        {{ campaign.name }}
                    </button>
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <Link
                            :href="route('campaigns.create')"
                            class="block px-3 py-2 text-indigo-600 hover:bg-gray-100 transition-colors"
                        >
                            + New Campaign
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Global Search -->
        <div class="flex-1 max-w-2xl mx-4">
            <div class="relative">
                <input
                    v-model="searchQuery"
                    @keyup.enter="handleSearch"
                    type="text"
                    placeholder="Search characters, places, plots..."
                    class="w-full px-4 py-2 pl-10 bg-gray-100 border border-transparent rounded-lg focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors"
                />
                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Right: User Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications (placeholder) -->
            <button class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- User Dropdown -->
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </template>

                <template #content>
                    <div class="px-4 py-2 border-b border-gray-200">
                        <div class="font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                    <DropdownLink :href="route('profile.edit')">
                        Profile
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
    </header>
</template>

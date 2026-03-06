<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import TopBar from '@/Components/Layout/TopBar.vue';
import ModeTabsNav from '@/Components/Layout/ModeTabsNav.vue';
import type { PageProps } from '@/types';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

interface CampaignPageProps extends PageProps {
    campaigns?: Campaign[];
    campaign?: Campaign;
}

const page = usePage<CampaignPageProps>();

const user = computed(() => page.props.auth.user);
const campaigns = computed(() => (page.props as CampaignPageProps).campaigns || []);
const currentCampaign = computed(() => (page.props as CampaignPageProps).campaign);

const currentMode = computed<'plan' | 'prep' | 'play'>(() => {
    const url = page.url;
    if (url.includes('/play')) return 'play';
    if (url.includes('/prep')) return 'prep';
    return 'plan';
});
</script>

<template>
    <div class="flex h-screen bg-graphite">
        <!-- Sidebar -->
        <Sidebar :campaign="currentCampaign" />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <TopBar
                :user="user"
                :campaigns="campaigns"
                :current-campaign="currentCampaign"
            />

            <!-- Page Header with Mode Tabs -->
            <div v-if="currentCampaign" class="bg-gunmetal border-b border-charcoal/30 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-8">
                    <h1 class="text-2xl font-bold text-white tracking-tight">
                        {{ currentCampaign.name }}
                    </h1>
                    <ModeTabsNav
                        :campaign="currentCampaign"
                        :current-mode="currentMode"
                    />
                </div>
                <Link
                    :href="route('settings.index')"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-arcane-grey hover:text-white border border-charcoal hover:border-arcane-grey/50 rounded-lg transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </Link>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <!-- Main Content with Page Transition -->
                <Transition
                    name="page"
                    mode="out-in"
                >
                    <div :key="page.url" class="p-6">
                        <slot />
                    </div>
                </Transition>
            </main>
        </div>
    </div>
</template>

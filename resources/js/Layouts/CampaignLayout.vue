<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import TopBar from '@/Components/Layout/TopBar.vue';
import ModeTabsNav from '@/Components/Layout/ModeTabsNav.vue';
import Breadcrumbs from '@/Components/Layout/Breadcrumbs.vue';
import type { BreadcrumbItem } from '@/Components/Layout/Breadcrumbs.vue';
import Toast from '@/Components/Toast.vue';
import type { PageProps } from '@/types';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

interface CampaignPageProps extends PageProps {
    campaigns?: Campaign[];
    campaign?: Campaign;
    breadcrumbs?: BreadcrumbItem[];
}

const page = usePage<CampaignPageProps>();

const user = computed(() => page.props.auth.user);
const campaigns = computed(() => (page.props as CampaignPageProps).campaigns || []);
const currentCampaign = computed(() => (page.props as CampaignPageProps).campaign);
const breadcrumbs = computed(() => (page.props as CampaignPageProps).breadcrumbs || []);

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

            <!-- Page Header with Mode Tabs and Breadcrumbs -->
            <div v-if="currentCampaign" class="bg-gunmetal border-b border-charcoal/30 px-6 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-8">
                        <h1 class="text-2xl font-bold text-white tracking-tight">
                            {{ currentCampaign.name }}
                        </h1>
                        <ModeTabsNav
                            :campaign="currentCampaign"
                            :current-mode="currentMode"
                        />
                    </div>
                    <Breadcrumbs
                        v-if="breadcrumbs.length > 0"
                        :items="breadcrumbs"
                    />
                </div>
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

        <!-- Toast Notifications -->
        <Toast />
    </div>
</template>

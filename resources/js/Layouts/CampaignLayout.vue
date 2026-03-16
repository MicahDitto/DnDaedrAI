<script setup lang="ts">
import { computed, ref } from 'vue';
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

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};
</script>

<template>
    <div class="flex h-screen bg-graphite">
        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="isSidebarOpen"
            @click="closeSidebar"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden"
        />

        <!-- Sidebar -->
        <div
            :class="[
                'fixed lg:static inset-y-0 left-0 z-50 lg:z-auto',
                'transition-transform duration-300 ease-in-out lg:transition-none',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <Sidebar :campaign="currentCampaign" @close="closeSidebar" />
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar with Hamburger -->
            <div class="flex items-center bg-gunmetal border-b border-charcoal/30">
                <!-- Hamburger Menu Button (Mobile Only) -->
                <button
                    @click="toggleSidebar"
                    class="lg:hidden p-4 text-arcane-grey hover:text-white transition-colors"
                    aria-label="Toggle sidebar"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- TopBar Component -->
                <div class="flex-1">
                    <TopBar
                        :user="user"
                        :campaigns="campaigns"
                        :current-campaign="currentCampaign"
                    />
                </div>
            </div>

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

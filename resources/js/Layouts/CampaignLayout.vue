<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import TopBar from '@/Components/Layout/TopBar.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

interface User {
    name: string;
    email: string;
}

interface PageProps {
    auth: {
        user: User;
    };
    campaigns?: Campaign[];
    campaign?: Campaign;
}

const page = usePage<PageProps>();

const user = computed(() => page.props.auth.user);
const campaigns = computed(() => page.props.campaigns || []);
const currentCampaign = computed(() => page.props.campaign);
</script>

<template>
    <div class="flex h-screen bg-gray-100">
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

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <!-- Page Header -->
                <div v-if="$slots.header" class="bg-white border-b border-gray-200 px-6 py-4">
                    <slot name="header" />
                </div>

                <!-- Main Content -->
                <div class="p-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

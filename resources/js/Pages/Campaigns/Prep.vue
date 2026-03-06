<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

defineProps<{
    campaign: Campaign;
    campaigns: Campaign[];
}>();
</script>

<template>
    <Head :title="`${campaign.name} - Prep`" />

    <CampaignLayout>
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <div class="w-20 h-20 rounded-full bg-arcane-purple/20 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-arcane-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Session Prep</h2>
            <p class="text-arcane-grey max-w-md">
                Prepare for your upcoming sessions. Organize notes, plan encounters, and set the stage for adventure.
            </p>
        </div>
    </CampaignLayout>
</template>

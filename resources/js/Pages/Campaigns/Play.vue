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
    <Head :title="`${campaign.name} - Play`" />

    <CampaignLayout>
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <div class="w-20 h-20 rounded-full bg-legendary-gold/20 flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-legendary-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Live Session</h2>
            <p class="text-arcane-grey max-w-md">
                Run your game in real-time. Track initiative, manage encounters, and bring your story to life.
            </p>
        </div>
    </CampaignLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Campaign {
    slug: string;
}

interface Props {
    campaign: Campaign;
    currentMode?: 'plan' | 'prep' | 'play';
}

const props = withDefaults(defineProps<Props>(), {
    currentMode: 'plan'
});

const tabs = [
    { id: 'plan', label: 'Plan', description: 'Campaign planning & world building', route: 'campaigns.show' },
    { id: 'prep', label: 'Prep', description: 'Session preparation & notes', route: 'campaigns.prep' },
    { id: 'play', label: 'Play', description: 'Active session tools', route: 'campaigns.play' },
] as const;
</script>

<template>
    <nav class="flex items-center gap-2">
        <Link
            v-for="tab in tabs"
            :key="tab.id"
            :href="route(tab.route, campaign.slug)"
            class="relative px-6 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
            :class="[
                currentMode === tab.id
                    ? 'bg-arcane-flow text-white shadow-glow-arcane'
                    : 'text-arcane-grey hover:text-white hover:bg-charcoal/50'
            ]"
            :title="tab.description"
        >
            {{ tab.label }}
        </Link>
    </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue';
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
    {
        id: 'plan',
        label: 'Plan',
        description: 'Campaign planning & world building',
        route: 'campaigns.show',
        activeClasses: 'bg-arcane-flow shadow-glow-arcane'
    },
    {
        id: 'prep',
        label: 'Prep',
        description: 'Session preparation & notes',
        route: 'campaigns.prep',
        activeClasses: 'bg-nature-flow shadow-glow-nature'
    },
    {
        id: 'play',
        label: 'Play',
        description: 'Active session tools',
        route: 'campaigns.play',
        activeClasses: 'bg-legendary-flow shadow-glow-legendary'
    },
] as const;

const tabRefs = ref<HTMLElement[]>([]);
const indicatorStyle = ref({ width: '0px', left: '0px' });

const activeTabIndex = computed(() => {
    return tabs.findIndex(tab => tab.id === props.currentMode);
});

const updateIndicator = () => {
    const activeTab = tabRefs.value[activeTabIndex.value];
    if (activeTab) {
        indicatorStyle.value = {
            width: `${activeTab.offsetWidth}px`,
            left: `${activeTab.offsetLeft}px`
        };
    }
};

onMounted(() => {
    nextTick(updateIndicator);
});

watch(() => props.currentMode, () => {
    nextTick(updateIndicator);
});
</script>

<template>
    <nav class="relative flex items-center gap-2">
        <Link
            v-for="(tab, index) in tabs"
            :key="tab.id"
            :ref="(el) => { if (el) tabRefs[index] = el as HTMLElement }"
            :href="route(tab.route, campaign.slug)"
            class="relative px-6 py-2 text-sm font-semibold rounded-lg transition-all duration-200 arcane-dust"
            :class="[
                currentMode === tab.id
                    ? [tab.activeClasses, 'text-white']
                    : 'text-arcane-grey hover:text-white hover:bg-charcoal/50'
            ]"
            :title="tab.description"
        >
            {{ tab.label }}
        </Link>

        <!-- Sliding indicator line -->
        <div
            class="absolute -bottom-1 h-0.5 rounded-full transition-all duration-300 ease-out"
            :class="{
                'bg-gradient-to-r from-arcane-purple to-arcane-periwinkle': currentMode === 'plan',
                'bg-gradient-to-r from-nature-dark to-nature': currentMode === 'prep',
                'bg-gradient-to-r from-legendary-gold to-legendary-amber': currentMode === 'play'
            }"
            :style="indicatorStyle"
        />
    </nav>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface Plot {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    confidence: string;
    is_secret: boolean;
    created_at: string;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    plots: Plot[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredPlots = computed(() => {
    if (!filterSubtype.value) return props.plots;
    return props.plots.filter(p => p.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        main_quest: 'bg-legendary-gold/20 text-legendary-gold',
        side_quest: 'bg-arcane-periwinkle/20 text-arcane-periwinkle',
        character_arc: 'bg-nature/20 text-nature',
        mystery: 'bg-arcane-purple/20 text-arcane-purple',
        conflict: 'bg-danger/20 text-danger-light',
    };
    return colors[subtype] || 'bg-charcoal text-arcane-grey';
};

const getSubtypeIcon = (subtype: string) => {
    const icons: Record<string, string> = {
        main_quest: 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
        side_quest: 'M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z',
        character_arc: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        mystery: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z',
        conflict: 'M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z',
    };
    return icons[subtype] || icons.side_quest;
};

const getConfidenceIcon = (confidence: string) => {
    const icons: Record<string, string> = {
        canon: 'text-nature',
        likely: 'text-arcane-periwinkle',
        rumor: 'text-legendary-gold',
        unknown: 'text-arcane-grey',
    };
    return icons[confidence] || icons.unknown;
};
</script>

<template>
    <Head :title="`Plots - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('campaigns.show', campaign.slug)"
                        class="text-arcane-grey hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Plots & Quests
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.plots.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                >
                    + New Plot
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <!-- Filters -->
                <div class="mb-6 flex items-center space-x-4">
                    <label class="text-sm font-medium text-arcane-grey">Filter by type:</label>
                    <select
                        v-model="filterSubtype"
                        class="bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm text-sm"
                    >
                        <option value="">All Plots</option>
                        <option v-for="(label, value) in subtypes" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="plots.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-arcane-grey"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-white">No plots yet</h3>
                        <p class="mt-2 text-sm text-arcane-grey">
                            Create quests, story arcs, and mysteries to drive your campaign.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.plots.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                            >
                                Create Plot
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- No matches for filter -->
                <div
                    v-else-if="filteredPlots.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 p-8 text-center text-arcane-grey"
                >
                    No plots match the selected filter.
                </div>

                <!-- Plot Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="plot in filteredPlots"
                        :key="plot.id"
                        :href="route('campaigns.plots.show', [campaign.slug, plot.slug])"
                        class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 hover:shadow-glow-arcane-sm hover:border-arcane-periwinkle/30 transition-all duration-200"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center">
                                    <svg
                                        class="w-5 h-5 mr-2 text-arcane-grey"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getSubtypeIcon(plot.subtype)" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-white flex items-center">
                                        {{ plot.name }}
                                        <svg
                                            v-if="plot.is_secret"
                                            class="w-4 h-4 ml-2 text-arcane-grey"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            title="Secret"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </h3>
                                </div>
                                <span
                                    :class="getSubtypeColor(plot.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ subtypes[plot.subtype] || plot.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="plot.summary"
                                class="text-sm text-arcane-grey mb-4 line-clamp-2"
                            >
                                {{ plot.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-arcane-grey">
                                <span :class="getConfidenceIcon(plot.confidence)">
                                    {{ plot.confidence }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { computed, ref } from 'vue';

interface Item {
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
    items: Item[];
    subtypes: Record<string, string>;
}>();

const filterSubtype = ref<string>('');

const filteredItems = computed(() => {
    if (!filterSubtype.value) return props.items;
    return props.items.filter(i => i.subtype === filterSubtype.value);
});

const getSubtypeColor = (subtype: string) => {
    const colors: Record<string, string> = {
        weapon: 'bg-danger/20 text-danger-light',
        armor: 'bg-arcane-periwinkle/20 text-arcane-periwinkle',
        artifact: 'bg-arcane-purple/20 text-arcane-purple',
        consumable: 'bg-nature/20 text-nature',
        treasure: 'bg-legendary-gold/20 text-legendary-gold',
        mundane: 'bg-charcoal text-arcane-grey',
    };
    return colors[subtype] || 'bg-charcoal text-arcane-grey';
};

const getSubtypeIcon = (subtype: string) => {
    const icons: Record<string, string> = {
        weapon: 'M14.25 6.75L7.5 13.5M7.5 6.75L14.25 13.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        armor: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
        artifact: 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z',
        consumable: 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 15.5m14.8-.2a2.25 2.25 0 01.7 2.052L19 21.75l-.937-2.063a2.25 2.25 0 00-1.5-1.237L12 18l4.563-.45a2.25 2.25 0 001.5-1.237L19 15.3z',
        treasure: 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        mundane: 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9',
    };
    return icons[subtype] || icons.mundane;
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
    <Head :title="`Items - ${campaign.name}`" />

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
                        Items
                    </h2>
                </div>
                <Link
                    :href="route('campaigns.items.create', campaign.slug)"
                    class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                >
                    + New Item
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
                        <option value="">All Items</option>
                        <option v-for="(label, value) in subtypes" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Empty State -->
                <div
                    v-if="items.length === 0"
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
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                            />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-white">No items yet</h3>
                        <p class="mt-2 text-sm text-arcane-grey">
                            Create weapons, armor, artifacts, and other items for your campaign.
                        </p>
                        <div class="mt-6">
                            <Link
                                :href="route('campaigns.items.create', campaign.slug)"
                                class="inline-flex items-center px-4 py-2 bg-arcane-flow border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-glow-arcane-sm hover:shadow-glow-arcane transition-all duration-200"
                            >
                                Create Item
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- No matches for filter -->
                <div
                    v-else-if="filteredItems.length === 0"
                    class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10 p-8 text-center text-arcane-grey"
                >
                    No items match the selected filter.
                </div>

                <!-- Item Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <Link
                        v-for="item in filteredItems"
                        :key="item.id"
                        :href="route('campaigns.items.show', [campaign.slug, item.slug])"
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getSubtypeIcon(item.subtype)" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-white flex items-center">
                                        {{ item.name }}
                                        <svg
                                            v-if="item.is_secret"
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
                                    :class="getSubtypeColor(item.subtype)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ subtypes[item.subtype] || item.subtype }}
                                </span>
                            </div>

                            <p
                                v-if="item.summary"
                                class="text-sm text-arcane-grey mb-4 line-clamp-2"
                            >
                                {{ item.summary }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-arcane-grey">
                                <span :class="getConfidenceIcon(item.confidence)">
                                    {{ item.confidence }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

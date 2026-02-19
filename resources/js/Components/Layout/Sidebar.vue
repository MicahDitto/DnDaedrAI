<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign?: Campaign;
}>();

const expandedSections = ref<Record<string, boolean>>({
    sessions: true,
    characters: false,
    places: false,
    plots: false,
    worldbuilding: false,
    tools: false,
    notes: false,
});

const toggleSection = (section: string) => {
    expandedSections.value[section] = !expandedSections.value[section];
};

const navItems = computed(() => [
    {
        key: 'dashboard',
        label: 'Dashboard',
        icon: 'home',
        href: props.campaign ? route('campaigns.show', props.campaign.slug) : route('dashboard'),
        expandable: false,
    },
    {
        key: 'sessions',
        label: 'Sessions',
        icon: 'calendar',
        expandable: true,
        children: [
            { label: 'Timeline View', href: '#' },
            { label: 'Session 0 (Setup)', href: '#' },
            { label: '+ Next Session', href: '#', isAction: true },
        ],
    },
    {
        key: 'characters',
        label: 'Characters',
        icon: 'users',
        expandable: true,
        children: [
            { label: 'Player Characters', href: '#' },
            { label: 'NPCs', href: '#' },
            { label: 'Villains', href: '#' },
            { label: 'Relationships Map', href: '#' },
        ],
    },
    {
        key: 'places',
        label: 'Places',
        icon: 'map',
        expandable: true,
        children: [
            { label: 'World Overview', href: '#' },
            { label: 'Regions', href: '#' },
            { label: 'Maps Gallery', href: '#' },
        ],
    },
    {
        key: 'plots',
        label: 'Plots',
        icon: 'book',
        expandable: true,
        children: [
            { label: 'Main Campaign Arc', href: '#' },
            { label: 'Character Arcs', href: '#' },
            { label: 'Side Quests', href: '#' },
            { label: 'Mysteries', href: '#' },
        ],
    },
    {
        key: 'worldbuilding',
        label: 'Worldbuilding',
        icon: 'globe',
        expandable: true,
        children: [
            { label: 'History & Timeline', href: '#' },
            { label: 'Lore & Legends', href: '#' },
            { label: 'Factions', href: '#' },
            { label: 'Religions', href: '#' },
            { label: 'Magic System', href: '#' },
        ],
    },
    {
        key: 'tools',
        label: 'Tools',
        icon: 'dice',
        expandable: true,
        children: [
            { label: 'Random Generators', href: '#' },
            { label: 'Encounter Builder', href: '#' },
            { label: 'NPC Quick-Gen', href: '#' },
        ],
    },
    {
        key: 'notes',
        label: 'Notes',
        icon: 'note',
        expandable: true,
        children: [
            { label: 'Session Notes', href: '#' },
            { label: 'DM Journal', href: '#' },
            { label: 'Scratch Pad', href: '#' },
        ],
    },
]);

const icons: Record<string, string> = {
    home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    map: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
    book: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    globe: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    dice: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    note: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    chevron: 'M19 9l-7 7-7-7',
};
</script>

<template>
    <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col h-full">
        <!-- Logo -->
        <div class="p-4 border-b border-gray-700">
            <Link :href="route('dashboard')" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-white">DnDaedrAI</span>
            </Link>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4">
            <template v-for="item in navItems" :key="item.key">
                <!-- Non-expandable item -->
                <template v-if="!item.expandable">
                    <Link
                        :href="item.href"
                        class="flex items-center px-4 py-2 text-sm hover:bg-gray-800 hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[item.icon]" />
                        </svg>
                        {{ item.label }}
                    </Link>
                </template>

                <!-- Expandable section -->
                <template v-else>
                    <button
                        @click="toggleSection(item.key)"
                        class="w-full flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-800 hover:text-white transition-colors"
                    >
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons[item.icon]" />
                            </svg>
                            {{ item.label }}
                        </span>
                        <svg
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': expandedSections[item.key] }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons.chevron" />
                        </svg>
                    </button>

                    <!-- Children -->
                    <div
                        v-show="expandedSections[item.key]"
                        class="ml-8 border-l border-gray-700"
                    >
                        <Link
                            v-for="child in item.children"
                            :key="child.label"
                            :href="child.href"
                            class="block px-4 py-1.5 text-sm hover:bg-gray-800 hover:text-white transition-colors"
                            :class="{ 'text-indigo-400': child.isAction }"
                        >
                            {{ child.label }}
                        </Link>
                    </div>
                </template>
            </template>
        </nav>

        <!-- Settings -->
        <div class="border-t border-gray-700 p-4">
            <Link
                href="#"
                class="flex items-center text-sm hover:text-white transition-colors"
            >
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </Link>
        </div>
    </aside>
</template>

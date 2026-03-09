<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    imageUrl?: string | null;
    entityName: string;
    entityType: 'character' | 'place' | 'item' | 'faction' | 'plot';
}>();

const hasImage = computed(() => !!props.imageUrl);

// Default icons for each entity type
const typeIcons: Record<string, string> = {
    character: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />`,
    place: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />`,
    item: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />`,
    faction: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />`,
    plot: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />`,
};

const iconPath = computed(() => typeIcons[props.entityType] || typeIcons.character);
</script>

<template>
    <div class="bg-gunmetal shadow-dark-md rounded-lg border border-arcane-periwinkle/10 overflow-hidden">
        <!-- Image or Placeholder -->
        <div class="aspect-square relative">
            <img
                v-if="hasImage"
                :src="imageUrl!"
                :alt="entityName"
                class="w-full h-full object-cover"
            />
            <div
                v-else
                class="w-full h-full flex items-center justify-center bg-charcoal/50"
            >
                <svg
                    class="w-24 h-24 text-arcane-grey/30"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    v-html="iconPath"
                />
            </div>
        </div>
    </div>
</template>

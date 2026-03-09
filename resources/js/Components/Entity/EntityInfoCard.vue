<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import MarkdownContent from './MarkdownContent.vue';

const props = defineProps<{
    name: string;
    type: string;
    subtype?: string | null;
    confidence: string;
    summary?: string | null;
    isSecret?: boolean;
    editUrl: string;
    backUrl: string;
}>();

const emit = defineEmits<{
    (e: 'delete'): void;
}>();

// Type/subtype badge colors
const getTypeColor = (type: string, subtype?: string | null) => {
    // Character subtypes
    if (type === 'character') {
        const characterColors: Record<string, string> = {
            pc: 'bg-nature/20 text-nature border-nature/30',
            npc: 'bg-arcane-periwinkle/20 text-arcane-periwinkle border-arcane-periwinkle/30',
            villain: 'bg-danger/20 text-danger-light border-danger/30',
            ally: 'bg-arcane-purple/20 text-arcane-purple border-arcane-purple/30',
            neutral: 'bg-charcoal text-arcane-grey border-charcoal',
        };
        return characterColors[subtype || 'neutral'] || characterColors.neutral;
    }

    // Other entity types
    const typeColors: Record<string, string> = {
        place: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
        item: 'bg-legendary-amber/20 text-legendary-amber border-legendary-amber/30',
        faction: 'bg-arcane-purple/20 text-arcane-purple border-arcane-purple/30',
        plot: 'bg-nature/20 text-nature border-nature/30',
    };
    return typeColors[type] || 'bg-charcoal text-arcane-grey border-charcoal';
};

const getTypeLabel = (type: string, subtype?: string | null) => {
    if (type === 'character' && subtype) {
        const labels: Record<string, string> = {
            pc: 'Player Character',
            npc: 'NPC',
            villain: 'Villain',
            ally: 'Ally',
            neutral: 'Neutral',
        };
        return labels[subtype] || subtype;
    }
    return type.charAt(0).toUpperCase() + type.slice(1);
};

const getConfidenceColor = (confidence: string) => {
    const colors: Record<string, string> = {
        canon: 'bg-nature/20 text-nature border-nature/30',
        likely: 'bg-arcane-periwinkle/20 text-arcane-periwinkle border-arcane-periwinkle/30',
        speculative: 'bg-legendary-amber/20 text-legendary-amber border-legendary-amber/30',
    };
    return colors[confidence] || colors.speculative;
};
</script>

<template>
    <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
        <!-- Header with title and actions -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <!-- Back button -->
                <Link
                    :href="backUrl"
                    class="text-arcane-grey hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                        {{ name }}
                        <svg
                            v-if="isSecret"
                            class="w-5 h-5 text-arcane-grey"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            title="Secret"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </h1>
                    <!-- Badges -->
                    <div class="flex items-center gap-2 mt-2">
                        <span
                            :class="[getTypeColor(type, subtype), 'px-2 py-0.5 rounded text-xs font-medium border']"
                        >
                            {{ getTypeLabel(type, subtype) }}
                        </span>
                        <span
                            :class="[getConfidenceColor(confidence), 'px-2 py-0.5 rounded text-xs font-medium border']"
                        >
                            {{ confidence }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                <Link
                    :href="editUrl"
                    class="inline-flex items-center px-3 py-1.5 bg-gunmetal border border-charcoal rounded-md font-medium text-xs text-arcane-grey uppercase tracking-wider hover:bg-charcoal hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </Link>
                <button
                    @click="emit('delete')"
                    class="inline-flex items-center px-3 py-1.5 bg-danger/10 border border-danger/30 rounded-md font-medium text-xs text-danger-light uppercase tracking-wider hover:bg-danger hover:text-white hover:border-danger transition-colors"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>

        <!-- Summary -->
        <div v-if="summary" class="mt-4 pt-4 border-t border-charcoal/50">
            <MarkdownContent :content="summary" />
        </div>
    </div>
</template>

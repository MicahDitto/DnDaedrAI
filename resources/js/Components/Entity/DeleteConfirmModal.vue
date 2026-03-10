<script setup lang="ts">
defineProps<{
    show: boolean;
    entityType: string;
    entityName: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
}>();

const getEntityTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        character: 'Character',
        place: 'Place',
        item: 'Item',
        faction: 'Faction',
        plot: 'Plot',
    };
    return labels[type] || type.charAt(0).toUpperCase() + type.slice(1);
};
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="emit('close')"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">
                        Delete {{ getEntityTypeLabel(entityType) }}
                    </h3>
                    <p class="text-sm text-arcane-grey mb-6">
                        Are you sure you want to delete "{{ entityName }}"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="emit('close')"
                            class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="emit('confirm')"
                            class="px-4 py-2 text-sm font-medium text-white bg-danger border border-transparent rounded-md hover:shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

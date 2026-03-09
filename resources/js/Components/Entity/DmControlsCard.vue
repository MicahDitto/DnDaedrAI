<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    isSecret: boolean;
    hasSecrets: boolean;
}>();

const emit = defineEmits<{
    (e: 'toggle-secrets-visibility', value: boolean): void;
}>();

const showSecrets = ref(false);

watch(showSecrets, (newVal) => {
    emit('toggle-secrets-visibility', newVal);
});
</script>

<template>
    <div class="bg-gunmetal shadow-dark-md rounded-lg p-4 border border-arcane-periwinkle/10">
        <h3 class="text-sm font-medium text-arcane-grey uppercase mb-4 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            DM Controls
        </h3>

        <div class="space-y-3">
            <!-- Is Secret Status -->
            <div class="flex items-center justify-between">
                <span class="text-sm text-arcane-grey">Hidden from players</span>
                <span
                    :class="[
                        'px-2 py-1 rounded text-xs font-medium',
                        isSecret
                            ? 'bg-danger/20 text-danger-light'
                            : 'bg-nature/20 text-nature'
                    ]"
                >
                    {{ isSecret ? 'Secret' : 'Visible' }}
                </span>
            </div>

            <!-- Show Secrets Toggle -->
            <div v-if="hasSecrets" class="flex items-center justify-between pt-2 border-t border-charcoal/50">
                <span class="text-sm text-arcane-grey">Show DM secrets</span>
                <button
                    @click="showSecrets = !showSecrets"
                    :class="[
                        'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                        showSecrets ? 'bg-arcane-flow' : 'bg-charcoal'
                    ]"
                >
                    <span
                        :class="[
                            'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                            showSecrets ? 'translate-x-6' : 'translate-x-1'
                        ]"
                    />
                </button>
            </div>
        </div>
    </div>
</template>

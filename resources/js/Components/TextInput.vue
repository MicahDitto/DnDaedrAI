<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="rounded-md bg-charcoal border-charcoal-dark text-slate-200 placeholder-slate-400 shadow-dark-sm transition-all duration-200 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle focus:shadow-glow-arcane-sm focus:text-white"
        v-model="model"
        ref="input"
    />
</template>

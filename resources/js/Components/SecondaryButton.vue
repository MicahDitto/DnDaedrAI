<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-charcoal bg-gunmetal px-4 py-2 text-xs font-semibold uppercase tracking-widest text-arcane-grey shadow-dark-sm transition-all duration-200 ease-in-out hover:border-arcane-periwinkle/50 hover:text-white focus:outline-none focus:ring-2 focus:ring-arcane-periwinkle focus:ring-offset-2 focus:ring-offset-graphite disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-arcane-periwinkle text-start text-base font-medium text-arcane-periwinkle bg-arcane-periwinkle/10 focus:outline-none focus:text-white focus:bg-arcane-periwinkle/20 focus:border-arcane-purple transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-arcane-grey hover:text-white hover:bg-gunmetal hover:border-charcoal focus:outline-none focus:text-white focus:bg-gunmetal focus:border-charcoal transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

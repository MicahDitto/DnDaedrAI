<script setup lang="ts">
interface Props {
    size?: 'sm' | 'md' | 'lg';
    variant?: 'arcane' | 'nature' | 'legendary';
}

withDefaults(defineProps<Props>(), {
    size: 'md',
    variant: 'arcane'
});
</script>

<template>
    <div
        class="rune-spinner"
        :class="[
            {
                'rune-spinner-sm': size === 'sm',
                'rune-spinner-lg': size === 'lg'
            }
        ]"
        role="status"
        aria-label="Loading"
    >
        <span class="sr-only">Loading...</span>
    </div>
</template>

<style scoped>
/* Variant colors */
.rune-spinner[variant="nature"]::before {
    border-color: rgba(164, 194, 168, 0.2);
    border-top-color: var(--color-nature);
}

.rune-spinner[variant="nature"]::after {
    background: radial-gradient(
        circle,
        var(--color-nature) 0%,
        transparent 70%
    );
}

.rune-spinner[variant="legendary"]::before {
    border-color: rgba(210, 187, 92, 0.2);
    border-top-color: var(--color-legendary-gold);
}

.rune-spinner[variant="legendary"]::after {
    background: radial-gradient(
        circle,
        var(--color-legendary-gold) 0%,
        transparent 70%
    );
}
</style>

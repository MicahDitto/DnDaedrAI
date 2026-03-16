<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

defineProps<{
    items: BreadcrumbItem[];
}>();
</script>

<template>
    <nav class="flex items-center space-x-2 text-sm">
        <template v-for="(item, index) in items" :key="index">
            <!-- Separator -->
            <svg
                v-if="index > 0"
                class="w-4 h-4 text-arcane-grey/50 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>

            <!-- Breadcrumb Item -->
            <Link
                v-if="item.href && index < items.length - 1"
                :href="item.href"
                class="text-arcane-grey hover:text-arcane-periwinkle transition-colors truncate max-w-[200px]"
            >
                {{ item.label }}
            </Link>
            <span
                v-else
                class="truncate max-w-[200px]"
                :class="index === items.length - 1 ? 'text-white font-medium' : 'text-arcane-grey'"
            >
                {{ item.label }}
            </span>
        </template>
    </nav>
</template>

<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Toast from '@/Components/Toast.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-graphite pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/" class="flex flex-col items-center space-y-2">
                <ApplicationLogo class="h-16 w-16" />
                <span class="text-2xl font-bold text-gradient-arcane">DnDaedrAI</span>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-gunmetal border border-arcane-periwinkle/20 px-6 py-4 shadow-dark-lg sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>

        <!-- Toast Notifications -->
        <Toast />
    </div>
</template>

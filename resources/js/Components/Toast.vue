<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface FlashMessages {
    success?: string;
    error?: string;
    status?: string;
}

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref<'success' | 'error' | 'status'>('success');

const hideToast = () => {
    show.value = false;
    setTimeout(() => {
        message.value = '';
    }, 300); // Wait for transition to complete
};

const showToast = (msg: string, msgType: 'success' | 'error' | 'status') => {
    message.value = msg;
    type.value = msgType;
    show.value = true;

    // Auto-hide after 5 seconds
    setTimeout(hideToast, 5000);
};

// Watch for flash messages from page props
watch(
    () => page.props.flash as FlashMessages,
    (flash) => {
        if (flash.success) {
            showToast(flash.success, 'success');
        } else if (flash.error) {
            showToast(flash.error, 'error');
        } else if (flash.status) {
            showToast(flash.status, 'status');
        }
    },
    { immediate: true, deep: true }
);

const toastClasses = {
    success: 'bg-nature text-graphite border-nature',
    error: 'bg-danger text-white border-danger',
    status: 'bg-arcane-periwinkle text-graphite border-arcane-periwinkle',
};

const iconPaths = {
    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    status: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
};
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show && message"
            class="fixed top-4 right-4 z-50 max-w-md w-full shadow-dark-lg pointer-events-auto"
        >
            <div
                :class="toastClasses[type]"
                class="rounded-lg border-2 p-4 shadow-glow-arcane-sm"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-6 w-6"
                            :class="type === 'success' ? 'text-graphite' : 'text-white'"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                :d="iconPaths[type]"
                            />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 pt-0.5">
                        <p class="text-sm font-medium">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            @click="hideToast"
                            class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="type === 'success' ? 'text-graphite hover:text-charcoal focus:ring-nature' : 'text-white hover:text-slate-200 focus:ring-white'"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

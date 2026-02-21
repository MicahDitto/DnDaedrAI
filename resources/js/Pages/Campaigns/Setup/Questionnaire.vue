<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { ref, computed } from 'vue';

interface Option {
    value: string;
    label: string;
    description?: string;
}

interface Question {
    key: string;
    title: string;
    description: string;
    type: 'single_select' | 'multi_select' | 'number' | 'text' | 'textarea';
    options?: Option[];
    min?: number;
    max?: number;
    default?: number;
    placeholder?: string;
    max_selections?: number;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
    status: string;
}

type ResponseValue = string | number | string[] | null;

const props = defineProps<{
    campaign: Campaign;
    questions: Question[];
    responses: Record<string, ResponseValue>;
}>();

const currentStep = ref(0);
const localResponses = ref<Record<string, ResponseValue>>({ ...props.responses });
const isSaving = ref(false);

const currentQuestion = computed(() => props.questions[currentStep.value]);
const totalSteps = computed(() => props.questions.length);
const progress = computed(() => ((currentStep.value + 1) / totalSteps.value) * 100);

const isFirstStep = computed(() => currentStep.value === 0);
const isLastStep = computed(() => currentStep.value === totalSteps.value - 1);

const currentResponse = computed<ResponseValue>({
    get: () => localResponses.value[currentQuestion.value.key] ?? null,
    set: (value: ResponseValue) => {
        localResponses.value[currentQuestion.value.key] = value;
    }
});

const canProceed = computed(() => {
    const response = currentResponse.value;
    const question = currentQuestion.value;

    // Optional questions (text/textarea without required)
    if (question.type === 'text' || question.type === 'textarea') {
        return true;
    }

    // Required questions
    if (!response) return false;
    if (Array.isArray(response) && response.length === 0) return false;

    return true;
});

const saveResponse = async () => {
    isSaving.value = true;

    await router.post(
        route('campaigns.setup.store', props.campaign.slug),
        {
            question_key: currentQuestion.value.key,
            response: currentResponse.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isSaving.value = false;
            },
        }
    );
};

const nextStep = async () => {
    if (currentResponse.value !== undefined) {
        await saveResponse();
    }

    if (!isLastStep.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (!isFirstStep.value) {
        currentStep.value--;
    }
};

const complete = async () => {
    if (currentResponse.value !== undefined) {
        await saveResponse();
    }

    router.post(route('campaigns.setup.complete', props.campaign.slug));
};

const toggleMultiSelect = (value: string) => {
    const current = (currentResponse.value as string[]) || [];
    const index = current.indexOf(value);

    if (index === -1) {
        // Check max selections
        const maxSelections = currentQuestion.value.max_selections;
        if (maxSelections && current.length >= maxSelections) {
            return;
        }
        currentResponse.value = [...current, value];
    } else {
        currentResponse.value = current.filter(v => v !== value);
    }
};

const isSelected = (value: string) => {
    const response = currentResponse.value;
    if (Array.isArray(response)) {
        return response.includes(value);
    }
    return response === value;
};
</script>

<template>
    <Head :title="`Session 0 - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.show', campaign.slug)"
                    class="text-arcane-grey hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Session 0: Campaign Setup
                    </h2>
                    <p class="text-sm text-arcane-grey">{{ campaign.name }}</p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto">
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex justify-between text-sm text-arcane-grey mb-2">
                        <span>Question {{ currentStep + 1 }} of {{ totalSteps }}</span>
                        <span>{{ Math.round(progress) }}% complete</span>
                    </div>
                    <div class="h-2 bg-charcoal rounded-full overflow-hidden">
                        <div
                            class="h-full bg-arcane-flow transition-all duration-300"
                            :style="{ width: `${progress}%` }"
                        />
                    </div>
                </div>

                <!-- Question Card -->
                <div class="bg-gunmetal shadow-dark-md rounded-lg overflow-hidden border border-arcane-periwinkle/10">
                    <div class="p-8">
                        <!-- Question Header -->
                        <h3 class="text-2xl font-semibold text-white mb-2">
                            {{ currentQuestion.title }}
                        </h3>
                        <p class="text-arcane-grey mb-8">
                            {{ currentQuestion.description }}
                        </p>

                        <!-- Single Select -->
                        <div v-if="currentQuestion.type === 'single_select'" class="space-y-3">
                            <button
                                v-for="option in currentQuestion.options"
                                :key="option.value"
                                @click="currentResponse = option.value"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all"
                                :class="[
                                    isSelected(option.value)
                                        ? 'border-arcane-periwinkle bg-arcane-periwinkle/10'
                                        : 'border-charcoal hover:border-arcane-grey/50 hover:bg-charcoal/50'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-medium text-white">{{ option.label }}</span>
                                        <p v-if="option.description" class="text-sm text-arcane-grey mt-1">
                                            {{ option.description }}
                                        </p>
                                    </div>
                                    <div
                                        v-if="isSelected(option.value)"
                                        class="w-5 h-5 rounded-full bg-arcane-periwinkle flex items-center justify-center"
                                    >
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Multi Select -->
                        <div v-else-if="currentQuestion.type === 'multi_select'" class="space-y-3">
                            <p v-if="currentQuestion.max_selections" class="text-sm text-arcane-grey mb-4">
                                Select up to {{ currentQuestion.max_selections }} options
                            </p>
                            <button
                                v-for="option in currentQuestion.options"
                                :key="option.value"
                                @click="toggleMultiSelect(option.value)"
                                class="w-full text-left p-4 rounded-lg border-2 transition-all"
                                :class="[
                                    isSelected(option.value)
                                        ? 'border-arcane-periwinkle bg-arcane-periwinkle/10'
                                        : 'border-charcoal hover:border-arcane-grey/50 hover:bg-charcoal/50'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-medium text-white">{{ option.label }}</span>
                                        <p v-if="option.description" class="text-sm text-arcane-grey mt-1">
                                            {{ option.description }}
                                        </p>
                                    </div>
                                    <div
                                        class="w-5 h-5 rounded border-2 flex items-center justify-center"
                                        :class="isSelected(option.value) ? 'bg-arcane-periwinkle border-arcane-periwinkle' : 'border-charcoal'"
                                    >
                                        <svg v-if="isSelected(option.value)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Number -->
                        <div v-else-if="currentQuestion.type === 'number'" class="space-y-4">
                            <input
                                type="number"
                                :value="currentResponse as number | null"
                                @input="currentResponse = ($event.target as HTMLInputElement).valueAsNumber"
                                :min="currentQuestion.min"
                                :max="currentQuestion.max"
                                :placeholder="String(currentQuestion.default || '')"
                                class="w-32 text-center text-3xl font-semibold bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-lg"
                            />
                            <p v-if="currentQuestion.min && currentQuestion.max" class="text-sm text-arcane-grey">
                                ({{ currentQuestion.min }} - {{ currentQuestion.max }})
                            </p>
                        </div>

                        <!-- Text -->
                        <div v-else-if="currentQuestion.type === 'text'">
                            <input
                                type="text"
                                :value="currentResponse as string || ''"
                                @input="currentResponse = ($event.target as HTMLInputElement).value"
                                :placeholder="currentQuestion.placeholder"
                                class="w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-lg"
                            />
                        </div>

                        <!-- Textarea -->
                        <div v-else-if="currentQuestion.type === 'textarea'">
                            <textarea
                                :value="currentResponse as string || ''"
                                @input="currentResponse = ($event.target as HTMLTextAreaElement).value"
                                :placeholder="currentQuestion.placeholder"
                                rows="5"
                                class="w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-lg"
                            />
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="bg-charcoal/50 px-8 py-4 flex justify-between items-center border-t border-charcoal">
                        <button
                            v-if="!isFirstStep"
                            @click="prevStep"
                            class="px-4 py-2 text-arcane-grey hover:text-white transition-colors"
                        >
                            Back
                        </button>
                        <div v-else></div>

                        <div class="flex items-center space-x-4">
                            <span v-if="isSaving" class="text-sm text-arcane-grey">
                                Saving...
                            </span>

                            <button
                                v-if="!isLastStep"
                                @click="nextStep"
                                :disabled="!canProceed || isSaving"
                                class="px-6 py-2 bg-arcane-flow text-white rounded-lg hover:shadow-glow-arcane transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>

                            <button
                                v-else
                                @click="complete"
                                :disabled="isSaving"
                                class="px-6 py-2 bg-nature text-white rounded-lg hover:shadow-[0_0_15px_rgba(164,194,168,0.3)] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Complete Setup
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Skip Link -->
                <div class="mt-4 text-center">
                    <Link
                        :href="route('campaigns.show', campaign.slug)"
                        class="text-sm text-arcane-grey hover:text-white transition-colors"
                    >
                        Skip for now (you can complete this later)
                    </Link>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

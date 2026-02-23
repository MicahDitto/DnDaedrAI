<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

interface Provider {
    name: string;
    models: string[];
    key_prefix: string;
    key_help: string;
}

interface Settings {
    ai_provider: string;
    ai_preferences: {
        temperature: number;
        max_tokens: number;
        include_campaign_context: boolean;
        include_related_nodes: boolean;
    };
    masked_api_keys: Record<string, string>;
    has_openai_key: boolean;
    has_anthropic_key: boolean;
}

const props = defineProps<{
    settings: Settings;
    providers: Record<string, Provider>;
    defaultPreferences: Settings['ai_preferences'];
}>();

// Provider selection form
const providerForm = useForm({
    ai_provider: props.settings.ai_provider,
});

const updateProvider = () => {
    providerForm.put(route('settings.provider'), {
        preserveScroll: true,
    });
};

// API Key forms
const showApiKeyModal = ref(false);
const currentKeyProvider = ref<string>('');
const apiKeyForm = useForm({
    provider: '',
    api_key: '',
});

const openApiKeyModal = (provider: string) => {
    currentKeyProvider.value = provider;
    apiKeyForm.provider = provider;
    apiKeyForm.api_key = '';
    showApiKeyModal.value = true;
};

const saveApiKey = () => {
    apiKeyForm.put(route('settings.api-key'), {
        preserveScroll: true,
        onSuccess: () => {
            showApiKeyModal.value = false;
            apiKeyForm.reset();
        },
    });
};

const removeApiKey = (provider: string) => {
    if (confirm(`Are you sure you want to remove your ${props.providers[provider]?.name} API key?`)) {
        router.delete(route('settings.api-key.remove'), {
            data: { provider },
            preserveScroll: true,
        });
    }
};

// Test API key
const testingKey = ref<string | null>(null);
const testResult = ref<{ success: boolean; message: string } | null>(null);

const testApiKey = async (provider: string) => {
    testingKey.value = provider;
    testResult.value = null;

    try {
        const response = await fetch(route('settings.test-api-key'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ provider }),
        });

        testResult.value = await response.json();
    } catch (error) {
        testResult.value = {
            success: false,
            message: 'Failed to test API key. Please try again.',
        };
    } finally {
        testingKey.value = null;
    }
};

// Preferences form
const preferencesForm = useForm({
    temperature: props.settings.ai_preferences.temperature,
    max_tokens: props.settings.ai_preferences.max_tokens,
    include_campaign_context: props.settings.ai_preferences.include_campaign_context,
    include_related_nodes: props.settings.ai_preferences.include_related_nodes,
});

const updatePreferences = () => {
    preferencesForm.put(route('settings.preferences'), {
        preserveScroll: true,
    });
};

// Helper to check if provider has key
const hasKey = (provider: string) => {
    return provider === 'openai' ? props.settings.has_openai_key : props.settings.has_anthropic_key;
};

const currentProviderName = computed(() => {
    return props.providers[props.settings.ai_provider]?.name || props.settings.ai_provider;
});
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Settings
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- AI Provider Selection -->
                <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                    <h3 class="text-lg font-medium text-white mb-4">AI Provider</h3>
                    <p class="text-sm text-arcane-grey mb-4">
                        Select which AI provider to use for generating content. You'll need to provide your own API key.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button
                            v-for="(provider, key) in providers"
                            :key="key"
                            @click="providerForm.ai_provider = key; updateProvider()"
                            :class="[
                                'p-4 rounded-lg border-2 transition-all text-left',
                                providerForm.ai_provider === key
                                    ? 'border-arcane-periwinkle bg-arcane-periwinkle/10'
                                    : 'border-charcoal hover:border-arcane-grey'
                            ]"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-white">{{ provider.name }}</span>
                                <span
                                    v-if="hasKey(key)"
                                    class="text-xs px-2 py-1 bg-nature/20 text-nature rounded"
                                >
                                    Key Set
                                </span>
                                <span
                                    v-else
                                    class="text-xs px-2 py-1 bg-danger/20 text-danger-light rounded"
                                >
                                    No Key
                                </span>
                            </div>
                            <p class="text-sm text-arcane-grey mt-2">
                                Models: {{ provider.models.join(', ') }}
                            </p>
                        </button>
                    </div>

                    <div v-if="providerForm.recentlySuccessful" class="mt-3 text-sm text-nature">
                        Provider updated successfully.
                    </div>
                </div>

                <!-- API Keys -->
                <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                    <h3 class="text-lg font-medium text-white mb-4">API Keys</h3>
                    <p class="text-sm text-arcane-grey mb-4">
                        Your API keys are encrypted and stored securely. They are never logged or exposed.
                    </p>

                    <div class="space-y-4">
                        <div
                            v-for="(provider, key) in providers"
                            :key="key"
                            class="flex items-center justify-between p-4 bg-graphite rounded-lg border border-charcoal"
                        >
                            <div>
                                <div class="font-medium text-white">{{ provider.name }}</div>
                                <div class="text-sm text-arcane-grey">
                                    <template v-if="hasKey(key)">
                                        Key: {{ settings.masked_api_keys[key] || '***' }}
                                    </template>
                                    <template v-else>
                                        No API key configured
                                    </template>
                                </div>
                                <div class="text-xs text-arcane-grey mt-1">
                                    {{ provider.key_help }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <template v-if="hasKey(key)">
                                    <button
                                        @click="testApiKey(key)"
                                        :disabled="testingKey === key"
                                        class="px-3 py-1.5 text-sm bg-charcoal text-arcane-grey rounded hover:text-white transition-colors disabled:opacity-50"
                                    >
                                        {{ testingKey === key ? 'Testing...' : 'Test' }}
                                    </button>
                                    <button
                                        @click="openApiKeyModal(key)"
                                        class="px-3 py-1.5 text-sm bg-charcoal text-arcane-grey rounded hover:text-white transition-colors"
                                    >
                                        Update
                                    </button>
                                    <button
                                        @click="removeApiKey(key)"
                                        class="px-3 py-1.5 text-sm bg-danger/20 text-danger-light rounded hover:bg-danger/30 transition-colors"
                                    >
                                        Remove
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        @click="openApiKeyModal(key)"
                                        class="px-4 py-2 text-sm bg-arcane-periwinkle text-white rounded hover:bg-arcane-purple transition-colors"
                                    >
                                        Add Key
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Test Result -->
                    <div
                        v-if="testResult"
                        :class="[
                            'mt-4 p-3 rounded text-sm',
                            testResult.success
                                ? 'bg-nature/20 text-nature'
                                : 'bg-danger/20 text-danger-light'
                        ]"
                    >
                        {{ testResult.message }}
                    </div>
                </div>

                <!-- AI Preferences -->
                <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
                    <h3 class="text-lg font-medium text-white mb-4">AI Preferences</h3>
                    <p class="text-sm text-arcane-grey mb-4">
                        Customize how AI generates content for your campaigns.
                    </p>

                    <form @submit.prevent="updatePreferences" class="space-y-6">
                        <!-- Temperature -->
                        <div>
                            <label class="block text-sm font-medium text-arcane-grey mb-2">
                                Temperature: {{ preferencesForm.temperature }}
                            </label>
                            <input
                                type="range"
                                v-model.number="preferencesForm.temperature"
                                min="0"
                                max="2"
                                step="0.1"
                                class="w-full h-2 bg-charcoal rounded-lg appearance-none cursor-pointer accent-arcane-periwinkle"
                            />
                            <div class="flex justify-between text-xs text-arcane-grey mt-1">
                                <span>More Focused (0)</span>
                                <span>More Creative (2)</span>
                            </div>
                        </div>

                        <!-- Max Tokens -->
                        <div>
                            <label class="block text-sm font-medium text-arcane-grey mb-2">
                                Max Response Length
                            </label>
                            <select
                                v-model.number="preferencesForm.max_tokens"
                                class="w-full bg-charcoal border-charcoal text-white rounded-md shadow-sm focus:border-arcane-periwinkle focus:ring-arcane-periwinkle"
                            >
                                <option :value="500">Short (~500 tokens)</option>
                                <option :value="1000">Medium (~1000 tokens)</option>
                                <option :value="2000">Long (~2000 tokens)</option>
                                <option :value="4000">Very Long (~4000 tokens)</option>
                            </select>
                        </div>

                        <!-- Context Options -->
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="preferencesForm.include_campaign_context"
                                    class="rounded bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle"
                                />
                                <span class="ml-2 text-sm text-arcane-grey">
                                    Include campaign context in AI requests
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="preferencesForm.include_related_nodes"
                                    class="rounded bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle"
                                />
                                <span class="ml-2 text-sm text-arcane-grey">
                                    Include related characters/places/etc. for context
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center space-x-4">
                            <button
                                type="submit"
                                :disabled="preferencesForm.processing"
                                class="px-4 py-2 bg-arcane-periwinkle text-white rounded-md hover:bg-arcane-purple transition-colors disabled:opacity-50"
                            >
                                Save Preferences
                            </button>
                            <span v-if="preferencesForm.recentlySuccessful" class="text-sm text-nature">
                                Saved!
                            </span>
                        </div>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="bg-arcane-periwinkle/10 border border-arcane-periwinkle/30 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-arcane-periwinkle mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-arcane-grey">
                            <p class="font-medium text-white mb-1">About AI Features</p>
                            <p>
                                AI features will use your own API key, meaning usage costs go directly to your provider account.
                                We recommend setting usage limits in your provider's dashboard to avoid unexpected charges.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Key Modal -->
        <div v-if="showApiKeyModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showApiKeyModal = false"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">
                        {{ hasKey(currentKeyProvider) ? 'Update' : 'Add' }} {{ providers[currentKeyProvider]?.name }} API Key
                    </h3>

                    <form @submit.prevent="saveApiKey">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-arcane-grey mb-2">
                                API Key
                            </label>
                            <input
                                type="password"
                                v-model="apiKeyForm.api_key"
                                :placeholder="providers[currentKeyProvider]?.key_prefix + '...'"
                                class="w-full bg-charcoal border-charcoal text-white rounded-md shadow-sm focus:border-arcane-periwinkle focus:ring-arcane-periwinkle"
                                autocomplete="off"
                            />
                            <p class="mt-1 text-xs text-arcane-grey">
                                {{ providers[currentKeyProvider]?.key_help }}
                            </p>
                            <p v-if="apiKeyForm.errors.api_key" class="mt-1 text-xs text-danger-light">
                                {{ apiKeyForm.errors.api_key }}
                            </p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showApiKeyModal = false"
                                class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="apiKeyForm.processing || !apiKeyForm.api_key"
                                class="px-4 py-2 text-sm font-medium text-white bg-arcane-periwinkle rounded-md hover:bg-arcane-purple transition-colors disabled:opacity-50"
                            >
                                {{ apiKeyForm.processing ? 'Saving...' : 'Save Key' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

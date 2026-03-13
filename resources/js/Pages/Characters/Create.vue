<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
}>();

const form = useForm({
    name: '',
    subtype: 'npc',
    summary: '',
    content: {
        appearance: '',
        personality: '',
        motivation: '',
        secrets: '',
        voice_notes: '',
    },
    confidence: 'canon',
    is_secret: false,
    featured_image: null as File | null,
    gallery_images: [] as File[],
});

const featuredImagePreview = ref<string | null>(null);
const galleryImagePreviews = ref<string[]>([]);

const handleFeaturedImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.featured_image = file;
        featuredImagePreview.value = URL.createObjectURL(file);
    }
};

const clearFeaturedImage = () => {
    form.featured_image = null;
    featuredImagePreview.value = null;
};

const handleGalleryImagesChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = Array.from(target.files || []);
    form.gallery_images = [...form.gallery_images, ...files];
    galleryImagePreviews.value = [
        ...galleryImagePreviews.value,
        ...files.map(file => URL.createObjectURL(file))
    ];
};

const removeGalleryImage = (index: number) => {
    form.gallery_images.splice(index, 1);
    galleryImagePreviews.value.splice(index, 1);
};

const submit = () => {
    form.post(route('campaigns.characters.store', props.campaign.slug), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Create Character - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.characters.index', campaign.slug)"
                    class="text-arcane-grey hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Create Character
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <div class="bg-gunmetal overflow-hidden shadow-dark-md sm:rounded-lg border border-arcane-periwinkle/10">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Info Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Basic Information</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="name" value="Name" />
                                        <TextInput
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            autofocus
                                            placeholder="e.g., Gandalf the Grey"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <!-- Subtype -->
                                    <div>
                                        <InputLabel for="subtype" value="Character Type" />
                                        <select
                                            id="subtype"
                                            v-model="form.subtype"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            required
                                        >
                                            <option v-for="(label, value) in subtypes" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.subtype" class="mt-2" />
                                    </div>

                                    <!-- Confidence -->
                                    <div>
                                        <InputLabel for="confidence" value="Confidence Level" />
                                        <select
                                            id="confidence"
                                            v-model="form.confidence"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                        >
                                            <option v-for="(label, value) in confidenceLevels" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.confidence" class="mt-2" />
                                    </div>

                                    <!-- Summary -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="summary" value="Short Summary" />
                                        <textarea
                                            id="summary"
                                            v-model="form.summary"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="A brief one-liner about this character..."
                                            maxlength="500"
                                        />
                                        <InputError :message="form.errors.summary" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Character Details</h3>

                                <div class="space-y-6">
                                    <!-- Appearance -->
                                    <div>
                                        <InputLabel for="appearance" value="Appearance" />
                                        <textarea
                                            id="appearance"
                                            v-model="form.content.appearance"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What does this character look like?"
                                        />
                                    </div>

                                    <!-- Personality -->
                                    <div>
                                        <InputLabel for="personality" value="Personality" />
                                        <textarea
                                            id="personality"
                                            v-model="form.content.personality"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="How do they act? What are their traits?"
                                        />
                                    </div>

                                    <!-- Motivation -->
                                    <div>
                                        <InputLabel for="motivation" value="Motivation / Goals" />
                                        <textarea
                                            id="motivation"
                                            v-model="form.content.motivation"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What drives this character? What do they want?"
                                        />
                                    </div>

                                    <!-- Voice Notes -->
                                    <div>
                                        <InputLabel for="voice_notes" value="Voice / Mannerisms" />
                                        <textarea
                                            id="voice_notes"
                                            v-model="form.content.voice_notes"
                                            rows="2"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="Notes on how to roleplay this character..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Secrets Section -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">DM Secrets</h3>

                                <div class="space-y-4">
                                    <!-- Is Secret -->
                                    <div class="flex items-center">
                                        <input
                                            id="is_secret"
                                            v-model="form.is_secret"
                                            type="checkbox"
                                            class="h-4 w-4 bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle rounded"
                                        />
                                        <label for="is_secret" class="ml-2 block text-sm text-arcane-grey">
                                            This entire character is a secret (DM eyes only)
                                        </label>
                                    </div>

                                    <!-- Secrets -->
                                    <div>
                                        <InputLabel for="secrets" value="Character Secrets" />
                                        <textarea
                                            id="secrets"
                                            v-model="form.content.secrets"
                                            rows="3"
                                            class="mt-1 block w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                            placeholder="What secrets does this character have? What don't the players know yet?"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image Upload -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Featured Image</h3>

                                <div class="space-y-4">
                                    <!-- File Input -->
                                    <div>
                                        <InputLabel for="featured_image" value="Upload Image" />
                                        <input
                                            id="featured_image"
                                            type="file"
                                            accept="image/*"
                                            @change="handleFeaturedImageChange"
                                            class="mt-1 block w-full text-sm text-arcane-grey file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-arcane-purple/20 file:text-arcane-purple hover:file:bg-arcane-purple/30 file:cursor-pointer"
                                        />
                                        <InputError :message="form.errors.featured_image" class="mt-2" />
                                    </div>

                                    <!-- Preview -->
                                    <div v-if="featuredImagePreview" class="relative w-48 h-48">
                                        <img :src="featuredImagePreview" alt="Preview" class="w-full h-full object-cover rounded-lg border border-arcane-periwinkle/20" />
                                        <button
                                            type="button"
                                            @click="clearFeaturedImage"
                                            class="absolute top-2 right-2 p-1 bg-danger/80 hover:bg-danger rounded-full text-white transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images Upload -->
                            <div class="border-b border-charcoal/50 pb-6">
                                <h3 class="text-lg font-medium text-white mb-4">Gallery Images</h3>

                                <div class="space-y-4">
                                    <div>
                                        <InputLabel for="gallery_images" value="Upload Images" />
                                        <input
                                            id="gallery_images"
                                            type="file"
                                            accept="image/*"
                                            multiple
                                            @change="handleGalleryImagesChange"
                                            class="mt-1 block w-full text-sm text-arcane-grey file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-arcane-purple/20 file:text-arcane-purple hover:file:bg-arcane-purple/30 file:cursor-pointer"
                                        />
                                        <InputError :message="form.errors.gallery_images" class="mt-2" />
                                    </div>

                                    <!-- Gallery Previews -->
                                    <div v-if="galleryImagePreviews.length" class="grid grid-cols-3 gap-4">
                                        <div v-for="(preview, index) in galleryImagePreviews" :key="index" class="relative">
                                            <img :src="preview" alt="Gallery preview" class="w-full h-32 object-cover rounded-lg border border-arcane-periwinkle/20" />
                                            <button
                                                type="button"
                                                @click="removeGalleryImage(index)"
                                                class="absolute top-2 right-2 p-1 bg-danger/80 hover:bg-danger rounded-full text-white transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.characters.index', campaign.slug)"
                                    class="text-arcane-grey hover:text-white transition-colors"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Create Character
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

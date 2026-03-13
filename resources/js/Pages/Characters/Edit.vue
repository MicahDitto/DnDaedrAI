<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Character {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    summary: string | null;
    content: {
        appearance?: string;
        personality?: string;
        motivation?: string;
        secrets?: string;
        voice_notes?: string;
    };
    confidence: string;
    is_secret: boolean;
    featured_image?: {
        id: string;
        url: string;
        filename: string;
    } | null;
    gallery_images?: Array<{
        id: string;
        url: string;
        filename: string;
        order: number;
    }>;
}

interface Campaign {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    campaign: Campaign;
    character: Character;
    subtypes: Record<string, string>;
    confidenceLevels: Record<string, string>;
}>();

const form = useForm({
    name: props.character.name,
    subtype: props.character.subtype,
    summary: props.character.summary || '',
    content: {
        appearance: props.character.content?.appearance || '',
        personality: props.character.content?.personality || '',
        motivation: props.character.content?.motivation || '',
        secrets: props.character.content?.secrets || '',
        voice_notes: props.character.content?.voice_notes || '',
    },
    confidence: props.character.confidence,
    is_secret: props.character.is_secret,
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

const deleteFeaturedImage = () => {
    if (props.character.featured_image && confirm('Are you sure you want to delete this image?')) {
        router.delete(route('campaigns.characters.images.destroy', [props.campaign.slug, props.character.slug]), {
            data: { media_id: props.character.featured_image.id },
            preserveScroll: true,
        });
    }
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

const deleteGalleryImage = (mediaId: string) => {
    if (confirm('Are you sure you want to delete this image?')) {
        router.delete(route('campaigns.characters.images.destroy', [props.campaign.slug, props.character.slug]), {
            data: { media_id: mediaId },
            preserveScroll: true,
        });
    }
};

const submit = () => {
    form.put(route('campaigns.characters.update', [props.campaign.slug, props.character.slug]), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Edit ${character.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                    class="text-arcane-grey hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Edit: {{ character.name }}
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
                                    <!-- Current Featured Image -->
                                    <div v-if="character.featured_image && !featuredImagePreview" class="relative w-48 h-48">
                                        <img :src="character.featured_image.url" alt="Current featured image" class="w-full h-full object-cover rounded-lg border border-arcane-periwinkle/20" />
                                        <button
                                            type="button"
                                            @click="deleteFeaturedImage"
                                            class="absolute top-2 right-2 p-1 bg-danger/80 hover:bg-danger rounded-full text-white transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- File Input -->
                                    <div>
                                        <InputLabel for="featured_image" :value="character.featured_image ? 'Replace Image' : 'Upload Image'" />
                                        <input
                                            id="featured_image"
                                            type="file"
                                            accept="image/*"
                                            @change="handleFeaturedImageChange"
                                            class="mt-1 block w-full text-sm text-arcane-grey file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-arcane-purple/20 file:text-arcane-purple hover:file:bg-arcane-purple/30 file:cursor-pointer"
                                        />
                                        <InputError :message="form.errors.featured_image" class="mt-2" />
                                    </div>

                                    <!-- New Image Preview -->
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
                                    <!-- Current Gallery Images -->
                                    <div v-if="character.gallery_images && character.gallery_images.length > 0" class="space-y-2">
                                        <p class="text-sm text-arcane-grey">Current Images</p>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div v-for="image in character.gallery_images" :key="image.id" class="relative">
                                                <img :src="image.url" alt="Gallery image" class="w-full h-32 object-cover rounded-lg border border-arcane-periwinkle/20" />
                                                <button
                                                    type="button"
                                                    @click="deleteGalleryImage(image.id)"
                                                    class="absolute top-2 right-2 p-1 bg-danger/80 hover:bg-danger rounded-full text-white transition-colors"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add New Gallery Images -->
                                    <div>
                                        <InputLabel for="gallery_images" value="Add More Images" />
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

                                    <!-- New Gallery Images Previews -->
                                    <div v-if="galleryImagePreviews.length" class="space-y-2">
                                        <p class="text-sm text-arcane-grey">New Images to Upload</p>
                                        <div class="grid grid-cols-3 gap-4">
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
                            </div>

                            <!-- Submit -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('campaigns.characters.show', [campaign.slug, character.slug])"
                                    class="text-arcane-grey hover:text-white transition-colors"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Save Changes
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </CampaignLayout>
</template>

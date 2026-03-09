<script setup lang="ts">
import { computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = defineProps<{
    content: string | null | undefined;
}>();

// Configure marked for safe rendering
marked.setOptions({
    breaks: true,
    gfm: true,
});

const renderedHtml = computed(() => {
    if (!props.content) return '';
    const html = marked.parse(props.content) as string;
    return DOMPurify.sanitize(html);
});
</script>

<template>
    <div
        v-if="content"
        class="prose prose-invert prose-sm max-w-none
            prose-headings:text-white prose-headings:font-semibold
            prose-p:text-arcane-grey prose-p:leading-relaxed
            prose-strong:text-white prose-strong:font-semibold
            prose-em:text-arcane-periwinkle prose-em:italic
            prose-a:text-arcane-periwinkle prose-a:underline hover:prose-a:text-white
            prose-ul:text-arcane-grey prose-ol:text-arcane-grey
            prose-li:text-arcane-grey prose-li:marker:text-arcane-periwinkle
            prose-code:text-nature prose-code:bg-charcoal prose-code:px-1 prose-code:py-0.5 prose-code:rounded
            prose-pre:bg-charcoal prose-pre:border prose-pre:border-arcane-periwinkle/20
            prose-blockquote:border-l-arcane-periwinkle prose-blockquote:text-arcane-grey prose-blockquote:italic"
        v-html="renderedHtml"
    />
</template>

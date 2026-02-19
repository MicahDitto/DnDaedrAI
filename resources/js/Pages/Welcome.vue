<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="DnDaedrAI - Your AI Dungeon Master's Companion" />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900">
        <!-- Navigation -->
        <nav class="relative z-10 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="text-2xl font-bold text-white">
                    DnDaedrAI
                </div>
                <div v-if="canLogin" class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 text-white hover:text-purple-300 transition-colors"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-white hover:text-purple-300 transition-colors"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative z-10">
            <div class="max-w-7xl mx-auto px-6 pt-20 pb-32">
                <div class="text-center">
                    <!-- D20 Icon -->
                    <div class="mb-8 inline-flex items-center justify-center w-24 h-24 rounded-full bg-purple-600/20 border border-purple-500/30">
                        <svg class="w-12 h-12 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <polygon points="12,2 22,8.5 22,15.5 12,22 2,15.5 2,8.5" />
                            <line x1="12" y1="2" x2="12" y2="22" />
                            <line x1="2" y1="8.5" x2="22" y2="8.5" />
                            <line x1="2" y1="15.5" x2="12" y2="22" />
                            <line x1="22" y1="15.5" x2="12" y2="22" />
                        </svg>
                    </div>

                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                        Your AI Dungeon Master's
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                            Companion
                        </span>
                    </h1>

                    <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-12">
                        DnDaedrAI is an AI-powered campaign management platform that organizes your world into an interconnected knowledge graph and assists across Plan, Prep, and Play modes.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                        <Link
                            v-if="canRegister && !$page.props.auth.user"
                            :href="route('register')"
                            class="px-8 py-4 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-semibold text-lg shadow-lg shadow-purple-600/30"
                        >
                            Start Your Campaign
                        </Link>
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-8 py-4 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-semibold text-lg shadow-lg shadow-purple-600/30"
                        >
                            Go to Dashboard
                        </Link>
                        <a
                            href="#features"
                            class="px-8 py-4 border border-gray-600 text-white rounded-xl hover:bg-white/5 transition-colors font-semibold text-lg"
                        >
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Features Grid -->
                <div id="features" class="grid md:grid-cols-3 gap-8 mt-12">
                    <!-- Plan Mode -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-3">Plan Mode</h3>
                        <p class="text-gray-400">
                            Deep collaborative world and session design. Build your campaign with guided questionnaires, NPC workshops, and encounter designers.
                        </p>
                    </div>

                    <!-- Prep Mode -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-3">Prep Mode</h3>
                        <p class="text-gray-400">
                            Review, memorize, and rehearse. Flashcard systems, session rehearsals, and readiness dashboards to feel confident at the table.
                        </p>
                    </div>

                    <!-- Play Mode -->
                    <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="w-12 h-12 bg-red-600/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-3">Play Mode</h3>
                        <p class="text-gray-400">
                            Live session support with real-time AI assistance. Quick generators, smart suggestions, and automatic session notes.
                        </p>
                    </div>
                </div>

                <!-- Knowledge Graph Section -->
                <div class="mt-24 text-center">
                    <h2 class="text-3xl font-bold text-white mb-6">The Knowledge Graph</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto mb-12">
                        Everything in your campaign is a connected node. Characters link to their hometown, their faction, their arc, the sessions they appeared in, and the items they carry. This interconnected graph is what allows the AI to understand your world.
                    </p>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl mx-auto">
                        <div class="bg-purple-600/10 border border-purple-500/20 rounded-xl p-4">
                            <div class="text-2xl mb-2">Characters</div>
                            <div class="text-gray-400 text-sm">PCs, NPCs, Villains</div>
                        </div>
                        <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-4">
                            <div class="text-2xl mb-2">Places</div>
                            <div class="text-gray-400 text-sm">Worlds, Cities, Dungeons</div>
                        </div>
                        <div class="bg-green-600/10 border border-green-500/20 rounded-xl p-4">
                            <div class="text-2xl mb-2">Plots</div>
                            <div class="text-gray-400 text-sm">Arcs, Quests, Mysteries</div>
                        </div>
                        <div class="bg-yellow-600/10 border border-yellow-500/20 rounded-xl p-4">
                            <div class="text-2xl mb-2">Lore</div>
                            <div class="text-gray-400 text-sm">History, Factions, Magic</div>
                        </div>
                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="mt-24 text-center">
                    <p class="text-gray-400 mb-6">It's not a replacement for the DM. It's the DM's second brain.</p>
                    <Link
                        v-if="canRegister && !$page.props.auth.user"
                        :href="route('register')"
                        class="inline-flex px-8 py-4 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-semibold text-lg shadow-lg shadow-purple-600/30"
                    >
                        Create Your Free Account
                    </Link>
                    <Link
                        v-else-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex px-8 py-4 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-semibold text-lg shadow-lg shadow-purple-600/30"
                    >
                        Go to Your Campaigns
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-white/10 py-8 px-6">
            <div class="max-w-7xl mx-auto text-center text-gray-500 text-sm">
                DnDaedrAI &copy; 2026. Your AI-powered campaign companion.
            </div>
        </footer>
    </div>
</template>

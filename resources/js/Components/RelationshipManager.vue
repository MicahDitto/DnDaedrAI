<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Node {
    id: string;
    type: string;
    subtype?: string;
    name: string;
    slug: string;
}

interface Edge {
    id: number;
    type: string;
    label: string | null;
    strength: number | null;
    is_secret: boolean;
    source_node?: Node;
    target_node?: Node;
}

interface RelationshipType {
    value: string;
    label: string;
    bidirectional?: boolean;
    reverse?: string;
}

interface NodeGroup {
    type: string;
    label: string;
    nodes: Node[];
}

const props = defineProps<{
    campaignSlug: string;
    nodeId: string;
    nodeName: string;
    nodeType: string;
    initialOutgoingEdges?: Edge[];
    initialIncomingEdges?: Edge[];
}>();

const outgoingEdges = ref<Edge[]>(props.initialOutgoingEdges || []);
const incomingEdges = ref<Edge[]>(props.initialIncomingEdges || []);
const isLoading = ref(false);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingEdge = ref<Edge | null>(null);
const availableTargets = ref<NodeGroup[]>([]);
const relationshipTypes = ref<Record<string, RelationshipType[]>>({});
const error = ref<string | null>(null);

// Form state
const form = ref({
    target_node_id: '',
    type: 'knows',
    label: '',
    strength: null as number | null,
    is_secret: false,
    bidirectional: true,
});

const editForm = ref({
    type: '',
    label: '',
    strength: null as number | null,
    is_secret: false,
});

const hasRelationships = computed(() =>
    outgoingEdges.value.length > 0 || incomingEdges.value.length > 0
);

const allRelationshipTypes = computed(() => {
    const types: RelationshipType[] = [];
    Object.values(relationshipTypes.value).forEach(group => {
        types.push(...group);
    });
    return types;
});

const selectedTypeInfo = computed(() => {
    return allRelationshipTypes.value.find(t => t.value === form.value.type);
});

const typeColors: Record<string, string> = {
    character: 'text-arcane-periwinkle',
    place: 'text-nature',
    item: 'text-legendary-gold',
    faction: 'text-arcane-purple',
    plot: 'text-legendary-gold',
};

const getNodeRoute = (node: Node) => {
    const typeRoutes: Record<string, string> = {
        character: 'campaigns.characters.show',
        place: 'campaigns.places.show',
        item: 'campaigns.items.show',
        faction: 'campaigns.factions.show',
        plot: 'campaigns.plots.show',
    };
    const routeName = typeRoutes[node.type];
    if (routeName) {
        return route(routeName, [props.campaignSlug, node.slug]);
    }
    return '#';
};

const fetchRelationshipTypes = async () => {
    try {
        const response = await fetch(
            route('campaigns.edges.types', props.campaignSlug),
            { headers: { 'Accept': 'application/json' } }
        );
        const data = await response.json();
        relationshipTypes.value = data.types;
    } catch (e) {
        console.error('Failed to fetch relationship types:', e);
    }
};

const fetchAvailableTargets = async () => {
    try {
        const response = await fetch(
            route('campaigns.edges.targets', [props.campaignSlug, props.nodeId]),
            { headers: { 'Accept': 'application/json' } }
        );
        const data = await response.json();
        availableTargets.value = data.groups;
    } catch (e) {
        console.error('Failed to fetch available targets:', e);
    }
};

const openAddModal = async () => {
    error.value = null;
    form.value = {
        target_node_id: '',
        type: 'knows',
        label: '',
        strength: null,
        is_secret: false,
        bidirectional: true,
    };

    isLoading.value = true;
    await Promise.all([fetchRelationshipTypes(), fetchAvailableTargets()]);
    isLoading.value = false;
    showAddModal.value = true;
};

const openEditModal = (edge: Edge) => {
    error.value = null;
    editingEdge.value = edge;
    editForm.value = {
        type: edge.type,
        label: edge.label || '',
        strength: edge.strength,
        is_secret: edge.is_secret,
    };
    showEditModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
    error.value = null;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingEdge.value = null;
    error.value = null;
};

const createRelationship = async () => {
    if (!form.value.target_node_id || !form.value.type) {
        error.value = 'Please select a target and relationship type.';
        return;
    }

    isLoading.value = true;
    error.value = null;

    try {
        const response = await fetch(
            route('campaigns.edges.store', props.campaignSlug),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    source_node_id: props.nodeId,
                    target_node_id: form.value.target_node_id,
                    type: form.value.type,
                    label: form.value.label || null,
                    strength: form.value.strength,
                    is_secret: form.value.is_secret,
                    bidirectional: form.value.bidirectional && selectedTypeInfo.value?.bidirectional,
                }),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            error.value = data.error || 'Failed to create relationship.';
            return;
        }

        // Add the new edge to outgoing
        outgoingEdges.value.push(data.edge);

        // If bidirectional reverse was created, add it to incoming
        if (data.reverse_edge) {
            incomingEdges.value.push(data.reverse_edge);
        }

        closeAddModal();
    } catch (e) {
        error.value = 'An error occurred while creating the relationship.';
        console.error(e);
    } finally {
        isLoading.value = false;
    }
};

const updateRelationship = async () => {
    if (!editingEdge.value) return;

    isLoading.value = true;
    error.value = null;

    try {
        const response = await fetch(
            route('campaigns.edges.update', [props.campaignSlug, editingEdge.value.id]),
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    type: editForm.value.type,
                    label: editForm.value.label || null,
                    strength: editForm.value.strength,
                    is_secret: editForm.value.is_secret,
                }),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            error.value = data.error || 'Failed to update relationship.';
            return;
        }

        // Update the edge in the list
        const index = outgoingEdges.value.findIndex(e => e.id === editingEdge.value!.id);
        if (index !== -1) {
            outgoingEdges.value[index] = data.edge;
        }

        closeEditModal();
    } catch (e) {
        error.value = 'An error occurred while updating the relationship.';
        console.error(e);
    } finally {
        isLoading.value = false;
    }
};

const deleteRelationship = async (edge: Edge, isOutgoing: boolean) => {
    if (!confirm(`Are you sure you want to delete this relationship?`)) return;

    isLoading.value = true;

    try {
        const response = await fetch(
            route('campaigns.edges.destroy', [props.campaignSlug, edge.id]),
            {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            }
        );

        if (!response.ok) {
            const data = await response.json();
            alert(data.error || 'Failed to delete relationship.');
            return;
        }

        // Remove from the appropriate list
        if (isOutgoing) {
            outgoingEdges.value = outgoingEdges.value.filter(e => e.id !== edge.id);
        } else {
            incomingEdges.value = incomingEdges.value.filter(e => e.id !== edge.id);
        }
    } catch (e) {
        alert('An error occurred while deleting the relationship.');
        console.error(e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchRelationshipTypes();
});
</script>

<template>
    <div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-arcane-grey uppercase">Relationships</h3>
            <button
                @click="openAddModal"
                class="text-xs px-2 py-1 bg-charcoal text-arcane-periwinkle hover:bg-arcane-periwinkle/20 rounded transition-colors"
            >
                + Add
            </button>
        </div>

        <!-- No relationships -->
        <div v-if="!hasRelationships" class="text-sm text-arcane-grey">
            No relationships yet. Click "+ Add" to create one.
        </div>

        <!-- Relationships list -->
        <div v-else class="space-y-3">
            <!-- Outgoing relationships -->
            <div v-for="edge in outgoingEdges" :key="'out-' + edge.id" class="text-sm group flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <span class="text-arcane-grey">{{ edge.label || edge.type }}</span>
                    <Link
                        v-if="edge.target_node"
                        :href="getNodeRoute(edge.target_node)"
                        class="ml-1 hover:text-white transition-colors truncate"
                        :class="typeColors[edge.target_node.type] || 'text-arcane-periwinkle'"
                    >
                        {{ edge.target_node.name }}
                    </Link>
                    <span v-if="edge.is_secret" class="ml-1 text-xs text-arcane-grey">(secret)</span>
                </div>
                <div class="hidden group-hover:flex items-center space-x-1 ml-2">
                    <button
                        @click="openEditModal(edge)"
                        class="p-1 text-arcane-grey hover:text-white transition-colors"
                        title="Edit"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <button
                        @click="deleteRelationship(edge, true)"
                        class="p-1 text-arcane-grey hover:text-danger-light transition-colors"
                        title="Delete"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Incoming relationships -->
            <div v-for="edge in incomingEdges" :key="'in-' + edge.id" class="text-sm group flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <Link
                        v-if="edge.source_node"
                        :href="getNodeRoute(edge.source_node)"
                        class="hover:text-white transition-colors truncate"
                        :class="typeColors[edge.source_node.type] || 'text-arcane-periwinkle'"
                    >
                        {{ edge.source_node.name }}
                    </Link>
                    <span class="text-arcane-grey ml-1">{{ edge.label || edge.type }}</span>
                    <span v-if="edge.is_secret" class="ml-1 text-xs text-arcane-grey">(secret)</span>
                </div>
                <div class="hidden group-hover:flex items-center space-x-1 ml-2">
                    <button
                        @click="deleteRelationship(edge, false)"
                        class="p-1 text-arcane-grey hover:text-danger-light transition-colors"
                        title="Delete"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Relationship Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="closeAddModal"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">Add Relationship</h3>

                    <div v-if="isLoading" class="text-center py-8 text-arcane-grey">
                        Loading...
                    </div>

                    <form v-else @submit.prevent="createRelationship" class="space-y-4">
                        <!-- Error -->
                        <div v-if="error" class="p-3 bg-danger/20 border border-danger/30 rounded-md text-danger-light text-sm">
                            {{ error }}
                        </div>

                        <!-- Target Node -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Connect to</label>
                            <select
                                v-model="form.target_node_id"
                                class="w-full bg-charcoal border-charcoal text-slate-200 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                required
                            >
                                <option value="">Select a node...</option>
                                <optgroup v-for="group in availableTargets" :key="group.type" :label="group.label">
                                    <option v-for="node in group.nodes" :key="node.id" :value="node.id">
                                        {{ node.name }} ({{ node.subtype || node.type }})
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Relationship Type -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Relationship</label>
                            <select
                                v-model="form.type"
                                class="w-full bg-charcoal border-charcoal text-slate-200 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                required
                            >
                                <optgroup v-for="(types, category) in relationshipTypes" :key="category" :label="category.charAt(0).toUpperCase() + category.slice(1)">
                                    <option v-for="type in types" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Custom Label (optional) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Custom Label (optional)</label>
                            <input
                                v-model="form.label"
                                type="text"
                                class="w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                placeholder="Override the default label..."
                            />
                        </div>

                        <!-- Bidirectional toggle -->
                        <div v-if="selectedTypeInfo?.bidirectional" class="flex items-center">
                            <input
                                id="bidirectional"
                                v-model="form.bidirectional"
                                type="checkbox"
                                class="h-4 w-4 bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle rounded"
                            />
                            <label for="bidirectional" class="ml-2 block text-sm text-arcane-grey">
                                Create bidirectional relationship
                            </label>
                        </div>

                        <!-- Secret toggle -->
                        <div class="flex items-center">
                            <input
                                id="is_secret"
                                v-model="form.is_secret"
                                type="checkbox"
                                class="h-4 w-4 bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle rounded"
                            />
                            <label for="is_secret" class="ml-2 block text-sm text-arcane-grey">
                                This relationship is secret (DM only)
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="closeAddModal"
                                class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="isLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-arcane-flow rounded-md hover:shadow-glow-arcane transition-all disabled:opacity-50"
                            >
                                Create Relationship
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Relationship Modal -->
        <div v-if="showEditModal && editingEdge" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="closeEditModal"></div>
                <div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6 border border-arcane-periwinkle/20">
                    <h3 class="text-lg font-medium text-white mb-4">Edit Relationship</h3>

                    <form @submit.prevent="updateRelationship" class="space-y-4">
                        <!-- Error -->
                        <div v-if="error" class="p-3 bg-danger/20 border border-danger/30 rounded-md text-danger-light text-sm">
                            {{ error }}
                        </div>

                        <!-- Show connection -->
                        <div class="text-sm text-arcane-grey">
                            {{ nodeName }}
                            <span class="text-white mx-2">→</span>
                            {{ editingEdge.target_node?.name }}
                        </div>

                        <!-- Relationship Type -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Relationship</label>
                            <select
                                v-model="editForm.type"
                                class="w-full bg-charcoal border-charcoal text-slate-200 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                required
                            >
                                <optgroup v-for="(types, category) in relationshipTypes" :key="category" :label="category.charAt(0).toUpperCase() + category.slice(1)">
                                    <option v-for="type in types" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Custom Label -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Custom Label</label>
                            <input
                                v-model="editForm.label"
                                type="text"
                                class="w-full bg-charcoal border-charcoal text-slate-200 placeholder-slate-400 focus:border-arcane-periwinkle focus:ring-arcane-periwinkle rounded-md shadow-dark-sm"
                                placeholder="Override the default label..."
                            />
                        </div>

                        <!-- Secret toggle -->
                        <div class="flex items-center">
                            <input
                                id="edit_is_secret"
                                v-model="editForm.is_secret"
                                type="checkbox"
                                class="h-4 w-4 bg-charcoal border-charcoal text-arcane-periwinkle focus:ring-arcane-periwinkle rounded"
                            />
                            <label for="edit_is_secret" class="ml-2 block text-sm text-arcane-grey">
                                This relationship is secret
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="closeEditModal"
                                class="px-4 py-2 text-sm font-medium text-arcane-grey bg-gunmetal border border-charcoal rounded-md hover:bg-charcoal hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="isLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-arcane-flow rounded-md hover:shadow-glow-arcane transition-all disabled:opacity-50"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

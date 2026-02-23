# Frontend Architecture

## Technology Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| Vue.js | 3.x | Reactive UI framework |
| TypeScript | 5.x | Type-safe JavaScript |
| Inertia.js | 1.x | SPA-like navigation |
| Tailwind CSS | 3.x | Utility-first styling |
| Vite | 5.x | Build tool and dev server |

## Directory Structure

```
resources/js/
├── app.ts                    # Vue app initialization
├── bootstrap.ts              # Axios configuration
├── types/                    # (If needed) Shared TypeScript types
├── Components/
│   ├── Layout/
│   │   ├── Sidebar.vue       # Main campaign navigation
│   │   └── TopBar.vue        # Search bar and user menu
│   ├── RelationshipManager.vue   # Node relationship CRUD
│   ├── ApplicationLogo.vue   # App logo component
│   ├── PrimaryButton.vue     # Primary action button
│   ├── SecondaryButton.vue   # Secondary action button
│   ├── DangerButton.vue      # Destructive action button
│   ├── TextInput.vue         # Text input field
│   ├── InputLabel.vue        # Form label
│   ├── InputError.vue        # Validation error display
│   ├── Checkbox.vue          # Checkbox input
│   ├── Dropdown.vue          # Dropdown menu container
│   ├── DropdownLink.vue      # Dropdown menu item
│   ├── NavLink.vue           # Navigation link
│   ├── ResponsiveNavLink.vue # Mobile navigation link
│   └── Modal.vue             # Modal dialog
├── Layouts/
│   ├── CampaignLayout.vue    # Campaign pages with sidebar
│   ├── AuthenticatedLayout.vue   # Logged-in pages
│   └── GuestLayout.vue       # Login/register pages
└── Pages/
    ├── Welcome.vue           # Landing page
    ├── Dashboard.vue         # Dashboard (redirects)
    ├── Auth/                 # Authentication pages
    ├── Profile/              # User profile pages
    ├── Settings/             # User settings
    ├── Campaigns/            # Campaign management
    ├── Characters/           # Character CRUD
    ├── Places/               # Place CRUD
    ├── Items/                # Item CRUD
    ├── Factions/             # Faction CRUD
    ├── Plots/                # Plot CRUD
    └── Sessions/             # Game session CRUD
```

---

## Layouts

### CampaignLayout.vue

The primary layout for campaign-related pages. Features:
- Left sidebar with navigation (Sidebar.vue)
- Top bar with search and user menu (TopBar.vue)
- Main content area with slot
- Header slot for page title

```vue
<CampaignLayout>
    <template #header>
        <h2>Page Title</h2>
    </template>

    <!-- Main content -->
    <div>...</div>
</CampaignLayout>
```

### AuthenticatedLayout.vue

Simple layout for authenticated pages outside campaigns:
- Top navigation bar
- Main content area

Used by: Profile, Settings

### GuestLayout.vue

Minimal layout for unauthenticated pages:
- Centered card with glassmorphism effect
- Application logo

Used by: Login, Register, Forgot Password

---

## Components

### Layout Components

#### Sidebar.vue
Main navigation sidebar with:
- Collapsible sections (Sessions, Characters, Places, etc.)
- Active state highlighting
- Links to Settings

**Props:**
- `campaign?: Campaign` - Current campaign for building URLs

#### TopBar.vue
Top header bar with:
- Global search with debounced input
- Search results dropdown grouped by type
- Breadcrumb area (slot)

**Features:**
- 300ms debounce on search
- Results grouped by entity type
- Click to navigate, Escape to close

### Form Components

#### TextInput.vue
Styled text input with dark theme.

**Props:**
- `modelValue: string`
- `type?: string` (default: 'text')

#### InputLabel.vue
Form label with consistent styling.

**Props:**
- `value?: string`

#### InputError.vue
Validation error message display.

**Props:**
- `message?: string`

#### Checkbox.vue
Styled checkbox with focus ring.

**Props:**
- `checked: boolean`

### Button Components

#### PrimaryButton.vue
Arcane gradient button for primary actions.

```vue
<PrimaryButton :disabled="form.processing">
    Save
</PrimaryButton>
```

#### SecondaryButton.vue
Subtle button for secondary actions.

#### DangerButton.vue
Red button for destructive actions.

### Overlay Components

#### Modal.vue
Reusable modal dialog with backdrop blur.

**Props:**
- `show: boolean`
- `maxWidth?: string` (sm, md, lg, xl, 2xl)
- `closeable?: boolean`

**Slots:**
- Default slot for content

#### Dropdown.vue
Dropdown menu container with positioning.

**Props:**
- `align?: string` (left, right)
- `width?: string` (48)
- `contentClasses?: string`

**Slots:**
- `trigger` - Clickable trigger element
- `content` - Dropdown content

### RelationshipManager.vue

Embedded component for managing node relationships.

**Props:**
- `campaignSlug: string`
- `nodeId: string`
- `nodeName: string`
- `nodeType: string`
- `initialOutgoingEdges: Edge[]`
- `initialIncomingEdges: Edge[]`

**Features:**
- List of outgoing/incoming relationships
- Add relationship modal with target selection
- Edit relationship modal
- Delete with confirmation
- Bidirectional relationship support
- Hover to reveal edit/delete actions

---

## Pages

### Page Structure

Each page follows a consistent pattern:

```vue
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CampaignLayout from '@/Layouts/CampaignLayout.vue';
import { ref } from 'vue';

// TypeScript interfaces
interface Campaign { ... }
interface Character { ... }

// Props from controller
const props = defineProps<{
    campaign: Campaign;
    character: Character;
}>();

// Local state
const showDeleteModal = ref(false);

// Methods
const deleteCharacter = () => { ... };
</script>

<template>
    <Head :title="`${character.name} - ${campaign.name}`" />

    <CampaignLayout>
        <template #header>
            <h2>{{ character.name }}</h2>
        </template>

        <!-- Page content -->
    </CampaignLayout>
</template>
```

### CRUD Page Patterns

Each entity (Characters, Places, Items, Factions, Plots) has:

| Page | File | Purpose |
|------|------|---------|
| Index | `Index.vue` | List all entities with filters |
| Create | `Create.vue` | Form to create new entity |
| Show | `Show.vue` | View entity details |
| Edit | `Edit.vue` | Form to edit entity |

### Index Page Features
- Grid layout of entity cards
- Search/filter input
- Subtype badges with semantic colors
- Secret indicator icon
- Links to show/create pages

### Show Page Features
- Entity details display
- Edit and Delete buttons
- Sidebar with metadata
- RelationshipManager component
- Delete confirmation modal

### Create/Edit Page Features
- Form with all entity fields
- Subtype dropdown
- Content textarea fields
- Confidence level selector
- Secret checkbox
- Cancel and Save buttons

---

## TypeScript Patterns

### Defining Interfaces

Each page defines interfaces for its props:

```typescript
interface Edge {
    id: number;
    type: string;
    label: string | null;
    strength: number | null;
    is_secret: boolean;
    target_node?: {
        id: string;
        name: string;
        slug: string;
        type: string;
    };
    source_node?: {
        id: string;
        name: string;
        slug: string;
        type: string;
    };
}

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
    created_at: string;
    updated_at: string;
    outgoing_edges: Edge[];
    incoming_edges: Edge[];
}
```

### Using Forms

```typescript
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: props.character?.name ?? '',
    subtype: props.character?.subtype ?? 'npc',
    summary: props.character?.summary ?? '',
    content: {
        appearance: props.character?.content?.appearance ?? '',
        // ...
    },
    confidence: props.character?.confidence ?? 'canon',
    is_secret: props.character?.is_secret ?? false,
});

const submit = () => {
    form.put(route('campaigns.characters.update', [
        props.campaign.slug,
        props.character.slug
    ]));
};
```

### Navigation

```typescript
import { router } from '@inertiajs/vue3';

// Navigate to a route
router.visit(route('campaigns.characters.show', [slug, nodeSlug]));

// Delete with redirect
router.delete(route('campaigns.characters.destroy', [slug, nodeSlug]));

// Preserve scroll position
router.put(url, data, { preserveScroll: true });
```

---

## Styling Patterns

### Dark Theme Classes

```html
<!-- Card -->
<div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">

<!-- Input -->
<input class="bg-charcoal border-charcoal text-white rounded-md
              focus:border-arcane-periwinkle focus:ring-arcane-periwinkle" />

<!-- Button -->
<button class="bg-arcane-periwinkle text-white rounded-md
               hover:bg-arcane-purple transition-colors">

<!-- Badge -->
<span class="bg-nature/20 text-nature px-2 py-0.5 rounded text-xs">
```

### Semantic Color Usage

| Color | Usage |
|-------|-------|
| `arcane-periwinkle` | Primary actions, links, focus states |
| `arcane-purple` | Hover states, secondary accent |
| `arcane-grey` | Muted text, labels |
| `nature` | Success, growth, positive |
| `legendary-gold` | Special, rare, important |
| `danger` | Destructive actions, errors |

---

## State Management

The application uses Vue's Composition API reactivity without a global store:

```typescript
// Local reactive state
const showModal = ref(false);
const searchQuery = ref('');
const isLoading = ref(false);

// Computed values
const filteredItems = computed(() =>
    items.value.filter(i => i.name.includes(searchQuery.value))
);

// Watchers
watch(searchQuery, debounce((value) => {
    fetchResults(value);
}, 300));
```

Page-level state is passed via Inertia props from the server, minimizing client-side state management needs.

---

## Common Patterns

### Delete Confirmation

```vue
<script setup>
const showDeleteModal = ref(false);

const deleteItem = () => {
    router.delete(route('...'), {
        onSuccess: () => showDeleteModal.value = false
    });
};
</script>

<template>
    <button @click="showDeleteModal = true">Delete</button>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 ...">
        <!-- Modal content -->
        <button @click="deleteItem">Confirm Delete</button>
        <button @click="showDeleteModal = false">Cancel</button>
    </div>
</template>
```

### Loading States

```vue
<script setup>
const isLoading = ref(false);

const fetchData = async () => {
    isLoading.value = true;
    try {
        // fetch...
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <button :disabled="isLoading">
        {{ isLoading ? 'Loading...' : 'Submit' }}
    </button>
</template>
```

### Debounced Search

```typescript
import { ref, watch } from 'vue';

const searchQuery = ref('');
const searchTimeout = ref<number | null>(null);

watch(searchQuery, (value) => {
    if (searchTimeout.value) clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
        performSearch(value);
    }, 300);
});
```

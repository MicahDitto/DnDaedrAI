# Architecture

## Overview

DnDaedrAI follows a modern full-stack architecture using Laravel as the backend with Inertia.js bridging to a Vue 3 frontend. This creates a monolithic application with the developer experience of an SPA.

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser                                  │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                    Vue 3 + TypeScript                    │    │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐      │    │
│  │  │   Pages     │  │  Layouts    │  │ Components  │      │    │
│  │  └─────────────┘  └─────────────┘  └─────────────┘      │    │
│  └─────────────────────────────────────────────────────────┘    │
│                              │                                   │
│                      Inertia.js Bridge                          │
│                              │                                   │
└──────────────────────────────┼──────────────────────────────────┘
                               │
┌──────────────────────────────┼──────────────────────────────────┐
│                         Laravel 12                               │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                     Controllers                          │    │
│  │  CampaignController, NodeController, EdgeController...   │    │
│  └─────────────────────────────────────────────────────────┘    │
│                              │                                   │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                    Eloquent Models                       │    │
│  │  User, Campaign, Node, Edge, Tag, GameSession...         │    │
│  └─────────────────────────────────────────────────────────┘    │
│                              │                                   │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                       SQLite                             │    │
│  └─────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

## Technology Stack

### Backend

| Component | Technology | Purpose |
|-----------|------------|---------|
| Framework | Laravel 12 | PHP web framework |
| ORM | Eloquent | Database abstraction |
| Auth | Laravel Breeze + Sanctum | Authentication scaffolding |
| Database | SQLite | File-based relational database |
| Validation | Laravel Validation | Request validation |

### Frontend

| Component | Technology | Purpose |
|-----------|------------|---------|
| Framework | Vue 3 | Reactive UI framework |
| Language | TypeScript | Type-safe JavaScript |
| Bridge | Inertia.js | SPA-like navigation without API |
| Routing | Ziggy | Laravel routes in JavaScript |
| Styling | Tailwind CSS | Utility-first CSS |
| Forms | @tailwindcss/forms | Form element styling |
| Build | Vite | Fast development and bundling |

## Request Flow

### Page Request
```
1. Browser requests /campaigns/my-campaign/characters
2. Laravel router matches to NodeController@charactersIndex
3. Controller fetches data from database
4. Controller returns Inertia::render('Characters/Index', $data)
5. Inertia sends JSON response with component name + props
6. Vue renders Characters/Index.vue with provided props
```

### Form Submission
```
1. Vue form calls router.post('/campaigns/slug/characters', data)
2. Inertia intercepts and sends XHR request
3. Laravel validates and processes
4. Controller redirects with flash message
5. Inertia updates page without full reload
```

## Directory Structure

### Backend (`app/`)

```
app/
├── Http/
│   └── Controllers/
│       ├── CampaignController.php    # Campaign CRUD
│       ├── NodeController.php        # Characters, Places, Items, Factions, Plots
│       ├── EdgeController.php        # Relationship management
│       ├── SessionController.php     # Game session management
│       ├── QuestionnaireController.php # Session 0 setup
│       ├── SearchController.php      # Global search
│       ├── SettingsController.php    # User settings & API keys
│       └── ProfileController.php     # User profile
├── Models/
│   ├── User.php                      # User account
│   ├── UserSetting.php               # AI settings & API keys
│   ├── Campaign.php                  # Campaign container
│   ├── Node.php                      # Generic entity (polymorphic)
│   ├── Edge.php                      # Relationship between nodes
│   ├── Tag.php                       # Organizational tags
│   ├── GameSession.php               # Game session records
│   └── QuestionnaireResponse.php     # Session 0 answers
└── Services/                         # (Future) Business logic
```

### Frontend (`resources/js/`)

```
resources/js/
├── app.ts                            # Vue app initialization
├── bootstrap.ts                      # Axios configuration
├── Components/
│   ├── Layout/
│   │   ├── Sidebar.vue               # Main navigation
│   │   └── TopBar.vue                # Search & user menu
│   ├── RelationshipManager.vue       # Edge CRUD component
│   ├── PrimaryButton.vue             # Styled buttons
│   ├── TextInput.vue                 # Form inputs
│   ├── Modal.vue                     # Modal dialogs
│   └── ...                           # Other UI components
├── Layouts/
│   ├── CampaignLayout.vue            # Campaign pages (sidebar + topbar)
│   ├── AuthenticatedLayout.vue       # Logged-in pages
│   └── GuestLayout.vue               # Login/register pages
└── Pages/
    ├── Campaigns/                    # Campaign management
    ├── Characters/                   # Character CRUD
    ├── Places/                       # Location CRUD
    ├── Items/                        # Item CRUD
    ├── Factions/                     # Faction CRUD
    ├── Plots/                        # Plot CRUD
    ├── Sessions/                     # Game session CRUD
    ├── Settings/                     # User settings
    └── Auth/                         # Authentication pages
```

## Key Patterns

### Node Polymorphism

The `Node` model is polymorphic - it represents different entity types:
- `type` field determines the entity type (character, place, item, faction, plot)
- `subtype` field provides further classification (e.g., npc, villain, ally for characters)
- `content` JSON field stores type-specific data

This allows a single table to store all entity types while maintaining flexibility.

### Campaign Scoping

All data is scoped to a campaign:
```php
// In controller
$campaign = Campaign::where('user_id', Auth::id())
    ->where('slug', $campaignSlug)
    ->firstOrFail();

$characters = $campaign->characters()->get();
```

This ensures users can only access their own data.

### Inertia Shared Data

Common data is shared across all requests:
```php
// In HandleInertiaRequests middleware
return [
    'auth' => ['user' => $request->user()],
    'flash' => ['success' => session('success')],
];
```

### TypeScript Interfaces

Each Vue page defines interfaces for its props:
```typescript
interface Character {
    id: string;
    name: string;
    slug: string;
    subtype: string;
    // ...
}

const props = defineProps<{
    campaign: Campaign;
    character: Character;
}>();
```

## Security

### Authentication
- Laravel Breeze handles registration, login, password reset
- Sanctum provides session-based authentication
- All routes are protected by `auth` middleware

### Authorization
- Controllers verify `user_id` matches `Auth::id()`
- Users can only access their own campaigns

### Data Protection
- API keys are encrypted using Laravel's `Crypt` facade
- Passwords are hashed automatically
- CSRF protection on all forms

## Future Architecture (AI)

The planned AI integration will add:

```
app/
├── Services/
│   ├── AIService.php           # AI orchestration
│   ├── OpenAIProvider.php      # OpenAI API wrapper
│   └── AnthropicProvider.php   # Anthropic API wrapper
├── Jobs/
│   └── GenerateAIContent.php   # Background AI generation
```

AI requests will:
1. Be queued for background processing
2. Use user-provided API keys
3. Include campaign context for relevant generation
4. Track token usage for cost awareness

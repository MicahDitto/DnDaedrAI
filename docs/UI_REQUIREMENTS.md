# UI Requirements & Vision Document

## Overview

This document defines the user experience vision for DnDaedrAI's three-mode system. The application follows a **connected flow** where Plan → Prep → Play represents a natural campaign lifecycle progression, with each mode building upon the previous.

**Priority Order:**
1. **Plan Mode** - Campaign creation and worldbuilding
2. **Play Mode** - Live session AI copilot
3. **Prep Mode** - Session preparation (bridging Plan and Play)

---

## Core Philosophy

### The DM's Journey

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                         │
│   PLAN MODE                    PREP MODE                   PLAY MODE    │
│   (Build the World)            (Prepare the Session)       (Run the Game)│
│                                                                         │
│   ┌─────────────┐             ┌─────────────┐             ┌───────────┐ │
│   │ Create      │    ───→     │ Select      │    ───→     │ AI-Powered│ │
│   │ Characters, │             │ Elements    │             │ Live      │ │
│   │ Places,     │             │ for Next    │             │ Session   │ │
│   │ Plots...    │             │ Session     │             │ Assistant │ │
│   └─────────────┘             └─────────────┘             └───────────┘ │
│         │                           │                           │       │
│         │                           │                           │       │
│         └───────────────────────────┴───────────────────────────┘       │
│                          Shared Knowledge Graph                         │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### Design Principles

1. **Context Preservation** - Switching modes maintains awareness of what you're working on
2. **Progressive Disclosure** - Complex features reveal themselves as needed
3. **AI as Collaborator** - AI augments DM creativity, never replaces it
4. **Quick Access** - Critical information is always one action away
5. **Visual Hierarchy** - Mode-specific colors guide focus (Arcane→Plan, Nature→Prep, Gold→Play)

---

## Mode 1: PLAN MODE

**Purpose:** Campaign creation hub - build the building blocks and piece them together into a cohesive world.

**Color Theme:** Arcane Purple (`bg-arcane-flow`)

### User Stories

#### Dashboard Overview
- As a DM, I want to see my campaign's health at a glance (stats, recent activity, upcoming sessions)
- As a DM, I want quick access to create new entities without navigating away
- As a DM, I want to see connections between entities to understand my world's structure

#### Worldbuilding
- As a DM, I want to create and manage Characters, Places, Items, Factions, Plots
- As a DM, I want to define relationships between any entities
- As a DM, I want to visualize my world as a knowledge graph
- As a DM, I want AI assistance to generate content (NPCs, locations, plot hooks)

### Layout Vision

```
┌────────────────────────────────────────────────────────────────────────┐
│  [Sidebar]  │  [TopBar: Search | Campaign Switcher | User]             │
│             │──────────────────────────────────────────────────────────│
│  Campaign   │  [Plan] [Prep] [Play]                    Mode Tabs       │
│  Navigation │──────────────────────────────────────────────────────────│
│             │                                                          │
│  • Dashboard│  ┌──────────────────────┐  ┌──────────────────────────┐ │
│  • Sessions │  │   CAMPAIGN STATS     │  │    QUICK ACTIONS         │ │
│  ──────────│  │   ▪ 12 Characters    │  │    [+ Character]         │ │
│  Knowledge  │  │   ▪ 8 Places         │  │    [+ Place]             │ │
│  ▸ Characters│  │   ▪ 3 Active Plots  │  │    [+ Plot]              │ │
│  ▸ Places   │  │   ▪ 5 Sessions       │  │    [AI Generate...]      │ │
│  ▸ Items    │  └──────────────────────┘  └──────────────────────────┘ │
│  ▸ Factions │                                                          │
│  ▸ Plots    │  ┌──────────────────────────────────────────────────────┐│
│  ──────────│  │              WORLD GRAPH VISUALIZATION               ││
│  Tools      │  │                                                      ││
│  ▸ Generator│  │     [Character]──────[Place]                        ││
│  ▸ Timeline │  │         │              │                            ││
│  ──────────│  │     [Faction]────────[Plot]                         ││
│  Notes      │  │                                                      ││
│             │  │         Interactive relationship explorer            ││
│             │  └──────────────────────────────────────────────────────┘│
│             │                                                          │
│             │  ┌─────────────────────┐  ┌────────────────────────────┐│
│             │  │  RECENT ACTIVITY    │  │  UPCOMING SESSIONS         ││
│             │  │  • Edited "Gundren" │  │  Session 6: The Mines      ││
│             │  │  • Added "Phandalin"│  │  Planned: Mar 15           ││
│             │  │  • New plot hook... │  │  [Go to Prep →]            ││
│             │  └─────────────────────┘  └────────────────────────────┘│
└────────────────────────────────────────────────────────────────────────┘
```

### Key Features

#### 1. Campaign Dashboard (Current: Partial)
**Status:** Exists but needs enhancement

| Feature | Current | Target |
|---------|---------|--------|
| Stats Cards | ✅ Clickable cards | Add trend indicators |
| Recent Activity | ✅ Basic list | Add activity type icons, time-relative dates |
| Sessions List | ✅ Basic list | Add "Go to Prep" quick action |
| Quick Actions | ✅ Add Character/Place | Add AI generation options |

#### 2. World Graph Visualization (New)
**Status:** Not started

- Interactive node-link diagram of campaign entities
- Click nodes to view/edit entities
- Drag to rearrange layout
- Filter by entity type
- Highlight relationship paths
- Zoom/pan for large graphs

**Technical Approach:**
- Consider: D3.js, vis.js, or Cytoscape.js
- Store layout positions per-user
- Server-side relationship data already exists (edges table)

#### 3. AI Content Generation (New)
**Status:** Settings exist, generation not implemented

| Generator | Input | Output |
|-----------|-------|--------|
| NPC Generator | Name, role, context | Full character with personality, appearance, motivations |
| Place Generator | Name, type, context | Description, atmosphere, notable features |
| Plot Hook Generator | Theme, involved entities | Story hook with stakes and complications |
| Name Generator | Culture, gender, type | Contextually appropriate names |

**UI Pattern:**
- Inline generation buttons on create/edit forms
- "AI Assist" panel that can generate and fill fields
- Review before accepting generated content

#### 4. Entity Management (Current: Complete)
**Status:** Full CRUD exists for all entity types

**Enhancements Needed:**
- Bulk operations (select multiple, tag, delete)
- Better empty states with guided actions
- Inline editing for quick updates
- Duplicate entity functionality

---

## Mode 2: PLAY MODE

**Purpose:** AI-powered live session assistant - your copilot during gameplay.

**Color Theme:** Legendary Gold (`bg-legendary-flow`)

### User Stories

#### AI Copilot
- As a DM, I want to ask the AI questions about my world during play
- As a DM, I want AI to generate NPC dialogue in character
- As a DM, I want AI to improvise descriptions when players go off-script
- As a DM, I want AI to suggest consequences for player actions

#### Quick Reference
- As a DM, I want instant access to NPC stats and personalities
- As a DM, I want to look up location details without breaking flow
- As a DM, I want to see relevant plot points for current scene

#### Session Tracking
- As a DM, I want to take notes during play that persist
- As a DM, I want to track what happened for recap later
- As a DM, I want to mark plot points as revealed/completed

### Layout Vision

```
┌────────────────────────────────────────────────────────────────────────┐
│  [Sidebar]  │  [TopBar: Search | Campaign | User]                      │
│  (Collapsed)│──────────────────────────────────────────────────────────│
│             │  [Plan] [Prep] [Play]                    Mode Tabs       │
│  [≡]        │──────────────────────────────────────────────────────────│
│             │                                                          │
│             │  ┌────────────────────────────────────────────────────┐  │
│             │  │                 AI COPILOT CHAT                    │  │
│             │  │                                                    │  │
│             │  │  You: The players just entered Phandalin. What     │  │
│             │  │       does Sildar say about the town?              │  │
│             │  │                                                    │  │
│             │  │  AI: *Speaking as Sildar Hallwinter*               │  │
│             │  │      "Ah, Phandalin! A rough frontier town, but    │  │
│             │  │       honest folk here. The Stonehill Inn should   │  │
│             │  │       have rooms, and you'll want to speak with    │  │
│             │  │       Harbin Wester at the townmaster's hall..."   │  │
│             │  │                                                    │  │
│             │  │  [Quick prompts: Describe | Dialogue | Improv]     │  │
│             │  │                                                    │  │
│             │  │  ┌────────────────────────────────────────────┐   │  │
│             │  │  │ Ask about your world...                    │   │  │
│             │  │  └────────────────────────────────────────────┘   │  │
│             │  └────────────────────────────────────────────────────┘  │
│             │                                                          │
│  [Pin]      │  ┌─────────────────┐  ┌─────────────────┐  ┌──────────┐ │
│  Quick      │  │ CURRENT SCENE   │  │ ACTIVE NPCs     │  │ NOTES    │ │
│  Access:    │  │                 │  │                 │  │          │ │
│             │  │ Location:       │  │ • Sildar        │  │ [Live    │ │
│  [Sildar]   │  │ Phandalin       │  │ • Gundren (?)   │  │  session │ │
│  [Phandalin]│  │                 │  │ • Townmaster    │  │  notes]  │ │
│  [Plot #3]  │  │ Plot Points:    │  │                 │  │          │ │
│             │  │ ▪ Find Gundren  │  │ [+ Add NPC]     │  │          │ │
│             │  │ ▪ Clear mines   │  │                 │  │          │ │
│             │  └─────────────────┘  └─────────────────┘  └──────────┘ │
└────────────────────────────────────────────────────────────────────────┘
```

### Key Features

#### 1. AI Copilot Chat (New - Core Feature)
**Status:** Not started - This is the centerpiece of Play mode

**Capabilities:**
| Prompt Type | Example | AI Response |
|-------------|---------|-------------|
| World Query | "What does the party know about the Black Spider?" | Summarizes known information from campaign data |
| NPC Dialogue | "Speak as Gundren when reunited with the party" | In-character dialogue based on NPC personality |
| Description | "Describe entering Wave Echo Cave" | Atmospheric description using place details |
| Improv | "A player asks about the dwarven runes" | Generates consistent lore on-the-fly |
| Consequence | "What happens if they kill the merchant?" | Suggests plot implications |

**Context Awareness:**
- AI has access to all campaign entities, relationships, session history
- Current scene context (location, present NPCs, active plots)
- Previous AI interactions in this session
- Player character information

**UI Elements:**
- Chat interface with markdown rendering
- Quick prompt buttons for common actions
- "Speak as [NPC]" selector
- Copy/save response to notes
- Regenerate option
- Context indicator showing what AI "knows"

#### 2. Quick Reference Panel (New)
**Status:** Not started

- Collapsible sidebar with pinned entities
- Search that opens inline preview (not full navigation)
- Pin/unpin entities for current session
- Shows entity summary + key details
- One-click to expand full details in modal

#### 3. Scene Tracker (New)
**Status:** Not started

- Current location display
- Active NPCs in scene
- Relevant plot points
- Automatically suggests based on location/NPCs

#### 4. Live Session Notes (New)
**Status:** Session notes exist but not live-capture

- Quick-capture text box
- Auto-timestamps entries
- Tag notes by type (event, revelation, quote)
- Persists to session record
- AI can summarize into recap post-session

#### 5. Sidebar Collapse (Enhancement)
**Status:** Sidebar exists, no collapse

- Minimize sidebar to icons only in Play mode
- Maximize screen real estate for AI chat
- Quick-access pins visible in collapsed state

---

## Mode 3: PREP MODE

**Purpose:** Session preparation - bridge between worldbuilding and live play.

**Color Theme:** Nature Green (`bg-nature-flow`)

### User Stories

#### Session Planning
- As a DM, I want to select which entities are relevant for next session
- As a DM, I want to write session-specific notes and plans
- As a DM, I want to outline expected scenes/encounters
- As a DM, I want to prepare NPC dialogue/reactions

#### Content Curation
- As a DM, I want to pull in entities from Plan mode to prep
- As a DM, I want to create session-specific variations (e.g., "injured Gundren")
- As a DM, I want to set up "if/then" branches for player choices

#### Transition
- As a DM, I want to "start session" and move to Play mode with my prep loaded
- As a DM, I want prep items to become "active" context in Play mode

### Layout Vision

```
┌────────────────────────────────────────────────────────────────────────┐
│  [Sidebar]  │  [TopBar: Search | Campaign | User]                      │
│             │──────────────────────────────────────────────────────────│
│  Campaign   │  [Plan] [Prep] [Play]                    Mode Tabs       │
│  Navigation │──────────────────────────────────────────────────────────│
│             │                                                          │
│             │  SESSION 6: Into the Mines             [Start Session →] │
│             │  Planned: March 15, 2024                                 │
│             │                                                          │
│             │  ┌──────────────────────────────────────────────────────┐│
│             │  │                   SESSION PLAN                       ││
│             │  │                                                      ││
│             │  │  OPENING SCENE                                       ││
│             │  │  ┌────────────────────────────────────────────────┐ ││
│             │  │  │ Location: Wave Echo Cave Entrance              │ ││
│             │  │  │ NPCs: Nezznar (hidden), Gundren (captive)     │ ││
│             │  │  │ Description: Dark tunnel, sound of dripping...│ ││
│             │  │  └────────────────────────────────────────────────┘ ││
│             │  │                                                      ││
│             │  │  EXPECTED BEATS                                      ││
│             │  │  □ Find evidence of previous expedition             ││
│             │  │  □ Encounter bugbear patrol                         ││
│             │  │  □ Discover Forge of Spells                         ││
│             │  │  □ Confrontation with Black Spider                  ││
│             │  │                                                      ││
│             │  │  [+ Add Scene]  [+ Add Beat]                        ││
│             │  └──────────────────────────────────────────────────────┘│
│             │                                                          │
│             │  ┌─────────────────────┐  ┌────────────────────────────┐│
│             │  │ SESSION ENTITIES    │  │ DM NOTES                   ││
│             │  │                     │  │                            ││
│             │  │ Characters:         │  │ Remember: Gundren knows    ││
│             │  │ [Gundren] [Nezznar] │  │ the party saved his       ││
│             │  │ [Nundro]            │  │ brother. Emotional beat!   ││
│             │  │                     │  │                            ││
│             │  │ Places:             │  │ If players rest here,      ││
│             │  │ [Wave Echo Cave]    │  │ have Nezznar's spies       ││
│             │  │ [Forge of Spells]   │  │ report back.               ││
│             │  │                     │  │                            ││
│             │  │ [+ Pull from Plan]  │  │ Black Spider motivation:   ││
│             │  │                     │  │ wants Forge for Spider     ││
│             │  │ Items:              │  │ Queen, not personal gain.  ││
│             │  │ [Lightbringer]      │  │                            ││
│             │  └─────────────────────┘  └────────────────────────────┘│
└────────────────────────────────────────────────────────────────────────┘
```

### Key Features

#### 1. Session Workspace (New)
**Status:** Session CRUD exists, prep workspace does not

- Session-specific view with all prep materials
- Select/link entities from campaign
- Create scene outlines
- Expected story beats (checkboxes)

#### 2. Entity Staging (New)
**Status:** Not started

- Pull entities from Plan mode
- Create session-specific notes per entity
- "What does this NPC know?" quick notes
- "What might this NPC do?" preparation
- Secret information to reveal

#### 3. Scene Builder (New)
**Status:** Not started

- Define expected scenes
- Link location, NPCs, items per scene
- Write boxed text / descriptions
- Branch points for player choices

#### 4. Start Session Flow (New)
**Status:** Not started

- "Start Session" button transitions to Play mode
- Loads prepped entities into Play mode context
- Creates session record if not exists
- Sets session status to "in_progress"

---

## Connected Flow: Mode Transitions

### Plan → Prep
**Trigger:** User clicks "Prep" tab or "Go to Prep" on upcoming session

**Context Passed:**
- Currently selected session (if any)
- Recently edited entities (for quick pull)
- Campaign context

**UI Behavior:**
- If no session selected, show session selector
- Highlight entities not yet linked to any session
- Show "new since last session" indicator

### Prep → Play
**Trigger:** User clicks "Start Session" or "Play" tab

**Context Passed:**
- Current session being prepped
- All staged entities
- Scene outlines and notes
- DM notes and reminders

**UI Behavior:**
- Confirmation if prep seems incomplete
- Load staged entities into quick-access pins
- Set AI context to session prep materials
- Begin session timer/log

### Play → Plan
**Trigger:** User clicks "Plan" tab (usually after session)

**Context Passed:**
- Session notes taken during play
- Any entities mentioned/created
- AI conversation highlights

**UI Behavior:**
- Prompt to finalize session notes
- Offer AI recap generation
- Highlight entities that need updating
- Track plot progress changes

---

## Component Specifications

### Shared Components (Cross-Mode)

#### ModeContext Provider
Maintains state across mode switches:
```typescript
interface ModeContext {
    currentMode: 'plan' | 'prep' | 'play';
    activeSession: Session | null;
    pinnedEntities: Entity[];
    aiConversation: Message[];
    sessionNotes: Note[];
}
```

#### EntityCard (Compact)
Small entity reference card for embedding:
- Thumbnail/icon
- Name and type badge
- One-line summary
- Click to expand/navigate

#### EntityModal
Full entity view in overlay:
- All entity details
- Edit capability
- Relationship view
- Don't lose context when viewing

#### AIChatInterface
Reusable AI chat component:
- Message history
- Context indicator
- Quick prompts
- Streaming responses

### Plan Mode Components

| Component | Purpose |
|-----------|---------|
| CampaignDashboard | Stats, activity, sessions overview |
| WorldGraph | Interactive relationship visualization |
| EntityGrid | Filterable entity list with cards |
| AIGenerator | Content generation interface |
| QuickCreateModal | Fast entity creation without navigation |

### Play Mode Components

| Component | Purpose |
|-----------|---------|
| AICoilot | Main chat interface |
| QuickRefPanel | Pinned entity sidebar |
| SceneTracker | Current scene context |
| LiveNotes | Session note capture |
| NPCVoiceSelector | Choose NPC for AI dialogue |

### Prep Mode Components

| Component | Purpose |
|-----------|---------|
| SessionWorkspace | Main prep interface |
| SceneBuilder | Scene planning tool |
| EntityStaging | Select and annotate entities |
| BeatChecklist | Story beats with checkboxes |
| StartSessionButton | Transition to Play mode |

---

## Data Model Considerations

### New Models/Fields Needed

#### Session Enhancement
```typescript
interface Session {
    // Existing
    id: number;
    number: number;
    title: string;
    status: 'planned' | 'in_progress' | 'completed';
    plan: string;         // Session plan text
    notes: string;        // During-session notes
    recap: string;        // Post-session summary

    // New
    staged_entities: EntityReference[];  // Entities prepped for this session
    scenes: Scene[];                     // Planned scenes
    beats: Beat[];                       // Expected story beats
    ai_context: string;                  // Additional context for AI
    started_at: timestamp;               // When Play mode started
    ended_at: timestamp;                 // When session completed
}
```

#### Scene (New)
```typescript
interface Scene {
    id: number;
    session_id: number;
    order: number;
    title: string;
    location_id: string | null;  // Reference to place
    description: string;         // Boxed text
    npc_ids: string[];          // NPCs in scene
    item_ids: string[];         // Relevant items
    notes: string;              // DM notes
    branches: Branch[];         // If/then planning
}
```

#### Beat (New)
```typescript
interface Beat {
    id: number;
    session_id: number;
    scene_id: number | null;
    order: number;
    description: string;
    is_completed: boolean;
    completed_at: timestamp | null;
    outcome_notes: string | null;
}
```

#### AIInteraction (New)
```typescript
interface AIInteraction {
    id: number;
    session_id: number;
    user_message: string;
    ai_response: string;
    context_used: string[];     // Entity IDs referenced
    prompt_type: 'query' | 'dialogue' | 'describe' | 'improv';
    saved_to_notes: boolean;
    created_at: timestamp;
}
```

---

## Technical Implementation Notes

### AI Integration Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Frontend (Vue)                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │ AIChatInput  │→ │ AIService    │→ │ StreamingResponse    │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │ HTTP/SSE
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Backend (Laravel)                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │ AIController │→ │ AIService    │→ │ Context Builder      │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
│                             │                    │              │
│                             │         ┌──────────┴────────┐    │
│                             │         │ Entity Repository │    │
│                             │         └───────────────────┘    │
│                             ▼                                   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              OpenAI / Anthropic API                      │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

### Context Building Strategy
1. Always include: Campaign basics, current session, active location
2. Include relevant: NPCs in scene, active plot points
3. On-demand: Full entity details when specifically asked
4. Token budget: Prioritize recent context, summarize older

### Streaming Responses
- Use Server-Sent Events (SSE) for streaming
- Display tokens as they arrive
- Allow cancellation mid-stream

---

## Prototype Priorities

### Phase 1: Plan Mode Enhancement
1. World Graph visualization (D3.js or similar)
2. AI content generation (NPC/Place generators)
3. Quick-create modal for entities
4. Dashboard polish (trends, better empty states)

### Phase 2: Play Mode Core
1. AI Copilot chat interface
2. Context building system
3. Quick reference panel
4. Sidebar collapse

### Phase 3: Play Mode Enhancement
1. NPC voice selector
2. Scene tracker
3. Live notes capture
4. Session timer/tracking

### Phase 4: Prep Mode
1. Session workspace
2. Entity staging
3. Scene builder
4. Start session flow

### Phase 5: Integration
1. Mode transition flows
2. Context passing between modes
3. AI recap generation
4. Session completion workflow

---

## Open Questions

1. **Graph Library Choice** - D3.js (flexible, complex) vs vis.js (easy, less custom) vs Cytoscape (specialized)?

2. **AI Response Storage** - Store all AI interactions or just saved ones? Privacy implications?

3. **Offline Support** - Should prep/notes work offline? PWA considerations?

4. **Mobile Experience** - Play mode on tablet during sessions? Responsive priorities?

5. **Multi-DM Support** - Future consideration for shared campaigns?

---

## Success Metrics

| Metric | Target | How to Measure |
|--------|--------|----------------|
| Mode Usage | All 3 modes used | Analytics per mode |
| AI Adoption | 70%+ of sessions use AI copilot | AI interactions per session |
| Session Prep | 60%+ sessions have prep | Prep entities linked |
| Flow Completion | Plan→Prep→Play journey | Mode transitions tracked |
| Time Savings | Faster session prep | Self-reported + session length |

---

## Revision History

| Date | Version | Changes |
|------|---------|---------|
| 2024-03-09 | 0.1 | Initial requirements document |


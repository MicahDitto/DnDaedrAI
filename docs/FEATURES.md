# Features

## Overview

This document lists all features in DnDaedrAI, their current status, and implementation details.

**Legend:**
- ✅ Complete - Fully implemented and working
- 🚧 Partial - Basic functionality exists, needs enhancement
- 📋 Planned - In the roadmap, not yet started
- ❌ Placeholder - UI exists but no functionality

---

## Core Features

### Authentication ✅

| Feature | Status | Details |
|---------|--------|---------|
| User Registration | ✅ | Email, name, password |
| Login/Logout | ✅ | Session-based auth |
| Password Reset | ✅ | Email-based reset flow |
| Email Verification | ✅ | Optional verification |
| Profile Management | ✅ | Update name, email, password |
| Account Deletion | ✅ | With confirmation |

**Files:** `routes/auth.php`, `Pages/Auth/*`, `Pages/Profile/*`

---

### Campaign Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Campaign | ✅ | Name, description, genre, system |
| List Campaigns | ✅ | Card grid with status badges |
| View Campaign | ✅ | Dashboard with stats |
| Edit Campaign | ✅ | All fields editable |
| Delete Campaign | ✅ | With confirmation |
| Campaign Status | ✅ | setup, active, paused, completed |

**Files:** `CampaignController.php`, `Pages/Campaigns/*`

---

### Character Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Character | ✅ | Full form with all fields |
| List Characters | ✅ | Grid with subtype filters |
| View Character | ✅ | All details + relationships |
| Edit Character | ✅ | All fields editable |
| Delete Character | ✅ | With confirmation modal |
| Character Subtypes | ✅ | PC, NPC, Villain, Ally, Neutral |
| Secret Characters | ✅ | is_secret flag, eye icon |
| Confidence Levels | ✅ | Canon, Likely, Rumor, Unknown |

**Content Fields:**
- Appearance
- Personality
- Motivation
- Secrets (DM only)
- Voice/Mannerisms

**Files:** `NodeController.php` (charactersX methods), `Pages/Characters/*`

---

### Place Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Place | ✅ | Full form with all fields |
| List Places | ✅ | Grid with subtype badges |
| View Place | ✅ | Details + child places + characters |
| Edit Place | ✅ | All fields editable |
| Delete Place | ✅ | With confirmation |
| Place Subtypes | ✅ | City, Town, Village, Dungeon, etc. |
| Parent Places | ✅ | Hierarchical locations |
| Characters Here | ✅ | Shows characters living in place |

**Content Fields:**
- Description
- Atmosphere
- History
- Notable Features
- Secrets

**Files:** `NodeController.php` (placesX methods), `Pages/Places/*`

---

### Item Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Item | ✅ | Full form with all fields |
| List Items | ✅ | Grid with subtype badges |
| View Item | ✅ | Details + owner + location |
| Edit Item | ✅ | All fields editable |
| Delete Item | ✅ | With confirmation |
| Item Subtypes | ✅ | Weapon, Armor, Artifact, etc. |
| Item Owner | ✅ | Links to character |
| Item Location | ✅ | Links to place |

**Content Fields:**
- Description
- Properties
- History
- Secrets

**Files:** `NodeController.php` (itemsX methods), `Pages/Items/*`

---

### Faction Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Faction | ✅ | Full form with all fields |
| List Factions | ✅ | Grid with subtype badges |
| View Faction | ✅ | Details + members + allies/rivals |
| Edit Faction | ✅ | All fields editable |
| Delete Faction | ✅ | With confirmation |
| Faction Subtypes | ✅ | Guild, Government, Cult, etc. |
| Faction Members | ✅ | Shows member characters |
| Allied/Rival Factions | ✅ | Inter-faction relationships |

**Content Fields:**
- Description
- Goals
- Methods
- Resources
- History
- Secrets

**Files:** `NodeController.php` (factionsX methods), `Pages/Factions/*`

---

### Plot Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Plot | ✅ | Full form with all fields |
| List Plots | ✅ | Grid with subtype badges |
| View Plot | ✅ | Details + involved entities |
| Edit Plot | ✅ | All fields editable |
| Delete Plot | ✅ | With confirmation |
| Plot Subtypes | ✅ | Main, Side, Character, Faction |
| Involved Characters | ✅ | Shows characters in plot |
| Involved Places | ✅ | Shows locations in plot |
| Involved Factions | ✅ | Shows factions in plot |

**Content Fields:**
- Description
- Objectives
- Stakes
- Progress
- Secrets

**Files:** `NodeController.php` (plotsX methods), `Pages/Plots/*`

---

### Relationship Management ✅

| Feature | Status | Details |
|---------|--------|---------|
| View Relationships | ✅ | On entity Show pages |
| Add Relationship | ✅ | Modal with type selection |
| Edit Relationship | ✅ | Change type/label |
| Delete Relationship | ✅ | With confirmation |
| Bidirectional Creation | ✅ | Creates reverse relationship |
| Relationship Types | ✅ | 30+ types in 6 categories |
| Custom Relationships | ✅ | User-defined labels |

**Relationship Categories:**
- Social (knows, friends, enemies, family)
- Hierarchy (serves, commands, employs)
- Organization (member_of, leads, founded)
- Location (lives_in, rules_over, visited)
- Possession (owns, created, seeks)
- Plot (involves, caused, affected_by)

**Files:** `EdgeController.php`, `Components/RelationshipManager.vue`

---

### Game Sessions ✅

| Feature | Status | Details |
|---------|--------|---------|
| Create Session | ✅ | Number, title, date |
| List Sessions | ✅ | Ordered by session number |
| View Session | ✅ | Plan, notes, recap |
| Edit Session | ✅ | All fields editable |
| Delete Session | ✅ | With confirmation |
| Session Plan | ✅ | DM preparation notes |
| Session Notes | ✅ | During-session notes |
| Session Recap | ✅ | Post-session summary |
| Session Outcomes | 🚧 | Array field exists, minimal UI |

**Files:** `SessionController.php`, `Pages/Sessions/*`

---

### Session 0 Questionnaire ✅

| Feature | Status | Details |
|---------|--------|---------|
| Multi-step Form | ✅ | 4 steps with progress |
| Question Types | ✅ | Text, select, textarea |
| Save Progress | ✅ | Saves per question |
| Complete Setup | ✅ | Marks campaign active |
| Edit Responses | ✅ | Can revisit and change |

**Question Categories:**
1. Basics (genre, players, experience)
2. World (type, setting details)
3. Style (tone, play style)
4. Details (safety tools, inspirations)

**Files:** `QuestionnaireController.php`, `Pages/Campaigns/Setup/Questionnaire.vue`

---

### Global Search ✅

| Feature | Status | Details |
|---------|--------|---------|
| Search Input | ✅ | In top bar |
| Debounced Search | ✅ | 300ms delay |
| Results Dropdown | ✅ | Grouped by type |
| Search All Types | ✅ | Characters, places, items, etc. |
| Search Sessions | ✅ | Title, notes, recap |
| Navigate to Result | ✅ | Click to go to entity |

**Files:** `SearchController.php`, `Components/Layout/TopBar.vue`

---

### Settings ✅

| Feature | Status | Details |
|---------|--------|---------|
| AI Provider Selection | ✅ | OpenAI or Anthropic |
| API Key Storage | ✅ | Encrypted storage |
| API Key Testing | ✅ | Verify key works |
| API Key Masking | ✅ | Shows sk-...xxx format |
| AI Preferences | ✅ | Temperature, tokens, context |

**Files:** `SettingsController.php`, `UserSetting.php`, `Pages/Settings/Index.vue`

---

## UI/UX Features

### Dark Theme ✅

| Feature | Status | Details |
|---------|--------|---------|
| Custom Color Palette | ✅ | Mystic Arcanist theme |
| Gradient Buttons | ✅ | Arcane flow gradients |
| Glow Effects | ✅ | Focus and hover glows |
| Dark Cards | ✅ | Gunmetal with borders |
| Semantic Colors | ✅ | Nature, legendary, danger |

**Files:** `tailwind.config.js`, `resources/css/app.css`

---

### Navigation ✅

| Feature | Status | Details |
|---------|--------|---------|
| Collapsible Sidebar | ✅ | Expandable sections |
| Active State | ✅ | Highlighted current page |
| Breadcrumb Support | 🚧 | Space exists, not implemented |
| Mobile Navigation | 🚧 | Basic responsive, needs work |

---

## Placeholder Features

These appear in the sidebar but have no functionality:

### Worldbuilding Section ❌

| Feature | Status | Location |
|---------|--------|----------|
| History & Timeline | ❌ | Sidebar link → # |
| Lore & Legends | ❌ | Sidebar link → # |
| Religions | ❌ | Sidebar link → # |
| Magic System | ❌ | Sidebar link → # |

### Tools Section ❌

| Feature | Status | Location |
|---------|--------|----------|
| Random Generators | ❌ | Sidebar link → # |
| Encounter Builder | ❌ | Sidebar link → # |
| NPC Quick-Gen | ❌ | Sidebar link → # |

### Notes Section ❌

| Feature | Status | Location |
|---------|--------|----------|
| Session Notes | ❌ | Sidebar link → # |
| DM Journal | ❌ | Sidebar link → # |
| Scratch Pad | ❌ | Sidebar link → # |

---

## Planned Features

### AI Integration 📋

| Feature | Priority | Description |
|---------|----------|-------------|
| NPC Generator | High | Generate character details from name |
| Place Generator | High | Generate location descriptions |
| Session Recap | High | Summarize session notes |
| Plot Hooks | Medium | Generate adventure hooks |
| Encounter Builder | Medium | Balance encounters |
| Name Generator | Medium | Random names by culture |
| Item Generator | Low | Generate magic items |
| Dialogue Helper | Low | NPC conversation suggestions |

### Data Features 📋

| Feature | Priority | Description |
|---------|----------|-------------|
| Import/Export | High | JSON export of campaign |
| Backup System | Medium | Automatic backups |
| Campaign Cloning | Low | Duplicate a campaign |

### Visualization 📋

| Feature | Priority | Description |
|---------|----------|-------------|
| Relationship Graph | High | Visual node graph |
| Timeline View | Medium | Chronological events |
| Map Integration | Low | Location mapping |

### Collaboration 📋

| Feature | Priority | Description |
|---------|----------|-------------|
| Player Accounts | Low | Read-only player access |
| Shared Campaigns | Low | Multi-DM support |

---

## Technical Debt

| Issue | Priority | Description |
|-------|----------|-------------|
| Toast Notifications | High | Flash messages not displayed |
| Loading States | Medium | Inconsistent loading indicators |
| Error Handling | Medium | Better error messages |
| Test Coverage | Medium | No tests currently |
| Mobile Responsive | Low | Sidebar needs mobile treatment |

---

## Feature Request Process

To request a new feature:
1. Check if it's already in this document
2. Consider which category it belongs to
3. Evaluate dependencies on other features
4. Discuss implementation approach

Features should be implemented in order of:
1. Dependencies (what does it need?)
2. User impact (how many people benefit?)
3. Complexity (effort vs. value)
